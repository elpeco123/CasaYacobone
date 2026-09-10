<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\Producto;
use App\Models\Venta;
use App\Models\VentaItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReporteController extends Controller
{
    /**
     * Reporte de rendimiento de la tienda (mensual o anual) con gráficos y tablas.
     *
     * Filtros por query string:
     *  - tipo:  'mensual' | 'anual'   (default: 'mensual')
     *  - anio:  año a analizar        (default: año actual)
     *  - mes:   1-12, solo en mensual (default: mes actual)
     *  - orden: 'unidades' | 'total'  (orden del Top 10 por unidades, default: 'unidades')
     */
    public function rendimiento(Request $request): View
    {
        $filtros = $request->validate([
            'tipo' => ['nullable', 'in:mensual,anual'],
            'anio' => ['nullable', 'integer', 'min:2000', 'max:2100'],
            'mes' => ['nullable', 'integer', 'min:1', 'max:12'],
            'orden' => ['nullable', 'in:unidades,total'],
        ]);

        $tipo = $filtros['tipo'] ?? 'mensual';
        $ordenTop = $filtros['orden'] ?? 'unidades';

        // Años con ventas registradas (para el selector), calculados en hora
        // local a partir del rango real de datos + año actual como fallback.
        $extremos = Venta::selectRaw('MIN(created_at) as min_fecha, MAX(created_at) as max_fecha')->first();
        $aniosDisponibles = [];
        if ($extremos?->min_fecha && $extremos?->max_fecha) {
            $desde = Carbon::parse($extremos->min_fecha)->setTimezone(config('app.timezone'))->year;
            $hasta = Carbon::parse($extremos->max_fecha)->setTimezone(config('app.timezone'))->year;
            $aniosDisponibles = range($hasta, $desde);
        }
        if (! in_array(now()->year, $aniosDisponibles, true)) {
            array_unshift($aniosDisponibles, now()->year);
        }

        $anio = min($filtros['anio'] ?? now()->year, now()->year + 1);
        $mes = $filtros['mes'] ?? now()->month;

        // Rango del período seleccionado.
        if ($tipo === 'mensual') {
            $inicio = Carbon::create($anio, $mes, 1)->startOfDay();
            $fin = $inicio->copy()->endOfMonth()->endOfDay();
            // Clave de agrupación: día del mes (1..N).
            $buckets = range(1, $inicio->daysInMonth);
            $etiquetas = array_map(fn ($d) => (string) $d, $buckets);
        } else {
            $inicio = Carbon::create($anio, 1, 1)->startOfDay();
            $fin = Carbon::create($anio, 12, 31)->endOfDay();
            // Clave de agrupación: mes (1..12).
            $buckets = range(1, 12);
            $etiquetas = array_map(
                fn ($m) => ucfirst(Carbon::create($anio, $m, 1)->locale('es')->monthName),
                $buckets
            );
        }

        // La BD guarda created_at en UTC: los rangos se convierten a UTC para
        // consultar, pero las etiquetas y agrupaciones usan hora local.
        $inicioUtc = $inicio->copy()->setTimezone('UTC');
        $finUtc = $fin->copy()->setTimezone('UTC');

        // Ventas del período (una sola consulta, se agrega en PHP).
        $ventas = Venta::whereBetween('created_at', [$inicioUtc, $finUtc])->get(['id', 'total', 'tipo_pago', 'created_at']);

        // Índice por ID para búsqueda O(1) dentro de los loops (evita timeouts con miles de registros).
        $ventasPorId = $ventas->keyBy('id');

        // Items del período con producto y categoría (base de categorías, top 10 y ganancia).
        $items = VentaItem::whereHas('venta', fn ($q) => $q->whereBetween('created_at', [$inicioUtc, $finUtc]))
            ->with('producto.categoria')
            ->get(['id', 'venta_id', 'producto_id', 'cantidad', 'precio_compra', 'precio_unitario', 'subtotal']);

        // --- 1/2/3. Series por bucket: totales, cantidades, ticket y ganancia ---
        $totales = array_fill_keys($buckets, 0.0);
        $cantidades = array_fill_keys($buckets, 0);
        $ganancias = array_fill_keys($buckets, 0.0);
        foreach ($ventas as $venta) {
            $clave = $tipo === 'mensual' ? (int) $venta->created_at->day : (int) $venta->created_at->month;
            $totales[$clave] += (float) $venta->total;
            $cantidades[$clave]++;
        }

        // Cobertura de costos: % de lo vendido con precio_compra confiable (> 0).
        $baseItems = (float) $items->sum('subtotal');
        $baseConCosto = (float) $items->where('precio_compra', '>', 0)->sum('subtotal');
        $coberturaCosto = $baseItems > 0 ? $baseConCosto / $baseItems : 0.0;
        $conCostos = $coberturaCosto >= 0.8;

        if ($conCostos) {
            foreach ($items as $item) {
                $venta = $ventasPorId->get($item->venta_id);
                if (! $venta) {
                    continue;
                }
                $clave = $tipo === 'mensual' ? (int) $venta->created_at->day : (int) $venta->created_at->month;
                $ganancias[$clave] += (float) $item->subtotal - (float) $item->precio_compra * (int) $item->cantidad;
            }
        }

        $tickets = [];
        foreach ($buckets as $b) {
            $tickets[$b] = $cantidades[$b] > 0 ? round($totales[$b] / $cantidades[$b], 2) : 0;
        }

        // --- 4. Ventas por categoría (reales de la BD, ordenadas de mayor a menor) ---
        $porCategoria = [];
        foreach ($items as $item) {
            $nombre = $item->producto?->categoria?->nombre ?? 'Sin categoría';
            if (! isset($porCategoria[$nombre])) {
                $porCategoria[$nombre] = 0.0;
            }
            $porCategoria[$nombre] += (float) $item->subtotal;
        }
        arsort($porCategoria);
        $totalCategorias = array_sum($porCategoria) ?: 1;

        // --- 5 y 6. Agregado por producto (independiente: uno por unidades, otro por facturación) ---
        $porProducto = [];
        foreach ($items as $item) {
            $pid = $item->producto_id;
            if (! isset($porProducto[$pid])) {
                $porProducto[$pid] = [
                    'producto' => $item->producto?->nombre ?? 'Producto eliminado',
                    'categoria' => $item->producto?->categoria?->nombre ?? '—',
                    'unidades' => 0,
                    'total' => 0.0,
                    'costo' => 0.0,
                ];
            }
            $porProducto[$pid]['unidades'] += (int) $item->cantidad;
            $porProducto[$pid]['total'] += (float) $item->subtotal;
            $porProducto[$pid]['costo'] += (float) $item->precio_compra * (int) $item->cantidad;
        }

        $topUnidades = collect($porProducto)
            ->sortByDesc($ordenTop === 'total' ? 'total' : 'unidades')
            ->take(10)
            ->values()
            ->all();
        $topFacturacion = collect($porProducto)
            ->sortByDesc('total')
            ->take(10)
            ->values()
            ->all();

        // --- 7. Tabla de rentabilidad por producto ---
        $rentabilidad = [];
        if ($conCostos) {
            foreach ($porProducto as $p) {
                if ($p['total'] <= 0) {
                    continue;
                }
                $ganancia = $p['total'] - $p['costo'];
                $rentabilidad[] = [
                    'producto' => $p['producto'],
                    'ventas' => $p['total'],
                    'costo' => $p['costo'],
                    'ganancia' => $ganancia,
                    'margen' => round($ganancia / $p['total'] * 100, 1),
                ];
            }
            usort($rentabilidad, fn ($a, $b) => $b['ganancia'] <=> $a['ganancia']);
            $rentabilidad = array_slice($rentabilidad, 0, 15);
        }

        // --- 8. Medios de pago (valores reales registrados) ---
        $porPago = [];
        foreach ($ventas as $venta) {
            $medio = $venta->tipo_pago ?? 'efectivo';
            if (! isset($porPago[$medio])) {
                $porPago[$medio] = ['total' => 0.0, 'cantidad' => 0];
            }
            $porPago[$medio]['total'] += (float) $venta->total;
            $porPago[$medio]['cantidad']++;
        }
        $totalPagos = array_sum(array_column($porPago, 'total')) ?: 1;

        // --- 9. Tabla resumen del período + variación vs bucket anterior ---
        $resumen = [];
        $prevTotal = null;
        foreach ($buckets as $b) {
            $variacion = $prevTotal !== null && $prevTotal > 0
                ? round(($totales[$b] - $prevTotal) / $prevTotal * 100, 1)
                : null;
            $resumen[] = [
                'etiqueta' => $etiquetas[array_search($b, $buckets, true)],
                'total' => $totales[$b],
                'cantidad' => $cantidades[$b],
                'ticket' => $tickets[$b],
                'ganancia' => $conCostos ? $ganancias[$b] : null,
                'variacion' => $variacion,
            ];
            $prevTotal = $totales[$b];
        }

        // --- 10. Comparación con el año anterior (solo anual y si hay datos) ---
        // Usa rangos UTC del año/mes previo y agrupa en PHP con hora local.
        $comparacionAnual = null;
        $comparacionMes = null;
        if ($tipo === 'anual') {
            $prevInicioUtc = $inicio->copy()->subYear()->setTimezone('UTC');
            $prevFinUtc = $fin->copy()->subYear()->setTimezone('UTC');
            $ventasPrevias = Venta::whereBetween('created_at', [$prevInicioUtc, $prevFinUtc])
                ->get(['total', 'created_at']);
            if ($ventasPrevias->isNotEmpty()) {
                $seriePrevia = array_fill(0, 12, 0.0);
                foreach ($ventasPrevias as $vp) {
                    $seriePrevia[(int) $vp->created_at->month - 1] += (float) $vp->total;
                }
                $totalPrevio = array_sum($seriePrevia);
                $totalActual = array_sum(array_values($totales));
                $comparacionAnual = [
                    'anioPrevio' => $anio - 1,
                    'seriePrevia' => array_values(array_map(fn ($v) => round($v, 2), $seriePrevia)),
                    'variacion' => $totalPrevio > 0 ? round(($totalActual - $totalPrevio) / $totalPrevio * 100, 1) : null,
                ];
            }
        } else {
            // En mensual: compara contra el mismo mes del año anterior.
            $prevMesInicioUtc = $inicio->copy()->subYear()->setTimezone('UTC');
            $prevMesFinUtc = $fin->copy()->subYear()->setTimezone('UTC');
            $mesPrevioTotal = (float) Venta::whereBetween('created_at', [$prevMesInicioUtc, $prevMesFinUtc])
                ->sum('total');
            $mesPrevioCant = Venta::whereBetween('created_at', [$prevMesInicioUtc, $prevMesFinUtc])
                ->count();
            if ($mesPrevioTotal > 0) {
                $comparacionMes = [
                    'total' => $mesPrevioTotal,
                    'cantidad' => $mesPrevioCant,
                    'variacion' => round((array_sum(array_values($totales)) - $mesPrevioTotal) / $mesPrevioTotal * 100, 1),
                ];
            }
        }

        return view('reportes.rendimiento', [
            'tipo' => $tipo,
            'anio' => $anio,
            'mes' => $mes,
            'ordenTop' => $ordenTop,
            'aniosDisponibles' => $aniosDisponibles,
            'inicio' => $inicio,
            'fin' => $fin,
            'hayDatos' => $ventas->isNotEmpty(),
            'totalPeriodo' => array_sum(array_values($totales)),
            'cantidadPeriodo' => array_sum(array_values($cantidades)),
            'ticketPeriodo' => array_sum(array_values($cantidades)) > 0
                ? round(array_sum(array_values($totales)) / array_sum(array_values($cantidades)), 2)
                : 0,
            'gananciaPeriodo' => $conCostos ? array_sum(array_values($ganancias)) : null,
            'series' => [
                'etiquetas' => array_values($etiquetas),
                'totales' => array_values(array_map(fn ($v) => round($v, 2), $totales)),
                'cantidades' => array_values($cantidades),
                'tickets' => array_values($tickets),
                'ganancias' => array_values(array_map(fn ($v) => round($v, 2), $ganancias)),
            ],
            'categorias' => [
                'nombres' => array_keys($porCategoria),
                'totales' => array_values(array_map(fn ($v) => round($v, 2), $porCategoria)),
                'porcentajes' => array_values(array_map(fn ($v) => round($v / $totalCategorias * 100, 1), $porCategoria)),
            ],
            'topUnidades' => $topUnidades,
            'topFacturacion' => $topFacturacion,
            'topFacturacionChart' => array_map(fn ($p) => [
                'producto' => $p['producto'],
                'total' => $p['total'],
                'unidades' => $p['unidades'],
            ], $topFacturacion),
            'conCostos' => $conCostos,
            'coberturaCosto' => round($coberturaCosto * 100, 1),
            'rentabilidad' => $rentabilidad,
            'pagos' => $porPago,
            'totalPagos' => $totalPagos,
            'resumen' => $resumen,
            'comparacionAnual' => $comparacionAnual,
            'comparacionMes' => $comparacionMes,
        ]);
    }

    /**
     * Display the daily report.
     */
    public function diario(Request $request): View
    {
        $fecha = $request->filled('fecha')
            ? Carbon::parse($request->input('fecha'))
            : Carbon::today();

        // Ventas del día
        $ventasDelDia = Venta::with(['items.producto', 'user'])
            ->whereDate('created_at', $fecha)
            ->get();

        // Caja y cambio inicial de la fecha
        $cajaDia = Caja::with('user')->whereDate('fecha', $fecha)->latest()->first();
        $montoInicialCaja = $cajaDia ? (float) $cajaDia->monto_inicial : 0.0;

        // Desglose por forma de pago
        $ventasEfectivoDia = $ventasDelDia->where('tipo_pago', 'efectivo')->sum('total');
        $ventasTarjetaDia = $ventasDelDia->where('tipo_pago', 'tarjeta')->sum('total');
        $ventasFacturaDia = $ventasDelDia->where('tipo_pago', 'factura')->sum('total');

        // Total físico de efectivo en caja
        $totalEfectivoEnCaja = $montoInicialCaja + $ventasEfectivoDia;

        // Total vendido
        $totalVendido = $ventasDelDia->sum('total');

        // Cantidad de items vendidos
        $cantidadVendida = $ventasDelDia->sum(function ($venta) {
            return $venta->items->sum('cantidad');
        });

        // Valor del stock restante al costo
        $valorStockRestante = Producto::selectRaw('SUM(precio_compra * stock) as total')->value('total') ?? 0;

        // Valor del stock al inicio del día (stock restante + lo que se vendió hoy al costo)
        $valorVendidoAlCosto = VentaItem::whereHas('venta', function ($q) use ($fecha) {
            $q->whereDate('created_at', $fecha);
        })->with('producto')->get()->sum(function ($item) {
            $precioCompra = $item->precio_compra ?? $item->producto?->precio_compra ?? 0;

            return $precioCompra * $item->cantidad;
        });

        $valorStockInicial = $valorStockRestante + $valorVendidoAlCosto;
        $diferencia = $valorStockRestante - $valorStockInicial;

        return view('reportes.diario', compact(
            'fecha',
            'cajaDia',
            'montoInicialCaja',
            'ventasEfectivoDia',
            'ventasTarjetaDia',
            'ventasFacturaDia',
            'totalEfectivoEnCaja',
            'ventasDelDia',
            'totalVendido',
            'cantidadVendida',
            'valorStockRestante',
            'valorStockInicial',
            'diferencia'
        ));
    }
}
