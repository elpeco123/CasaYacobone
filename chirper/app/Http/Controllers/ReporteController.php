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

    /**
     * Display the weekly missing stock report.
     */
    public function semanal(Request $request): View
    {
        $fechaFin = $request->filled('fecha_fin')
            ? Carbon::parse($request->input('fecha_fin'))
            : Carbon::today();
        $fechaInicio = $fechaFin->copy()->subDays(6);

        // Productos con stock bajo
        $productosFaltantes = Producto::stockBajo()
            ->with('categoria')
            ->orderBy('stock')
            ->get();

        // Productos sin stock
        $productosSinStock = Producto::sinStock()
            ->with('categoria')
            ->get();

        // Productos más vendidos en la semana
        $productosMasVendidos = VentaItem::selectRaw('producto_id, SUM(cantidad) as total_vendido, SUM(subtotal) as total_monto')
            ->whereHas('venta', function ($q) use ($fechaInicio, $fechaFin) {
                $q->whereBetween('created_at', [$fechaInicio->startOfDay(), $fechaFin->endOfDay()]);
            })
            ->groupBy('producto_id')
            ->with('producto.categoria')
            ->orderByDesc('total_vendido')
            ->take(10)
            ->get();

        // Ventas de la semana por día
        $ventasPorDia = [];
        for ($i = 0; $i < 7; $i++) {
            $dia = $fechaInicio->copy()->addDays($i);
            $ventasPorDia[] = [
                'fecha' => $dia->format('d/m'),
                'dia' => $dia->locale('es')->isoFormat('ddd'),
                'total' => Venta::whereDate('created_at', $dia)->sum('total'),
                'cantidad' => Venta::whereDate('created_at', $dia)->count(),
            ];
        }

        return view('reportes.semanal', compact(
            'fechaInicio',
            'fechaFin',
            'productosFaltantes',
            'productosSinStock',
            'productosMasVendidos',
            'ventasPorDia'
        ));
    }
}
