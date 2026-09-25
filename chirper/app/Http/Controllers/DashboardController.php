<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\CategoriaGasto;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\Retiro;
use App\Models\Venta;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with KPIs.
     */
    public function index(): View
    {
        $hoy = Carbon::today();

        // Caja del día y cambio inicial
        $cajaHoy = Caja::with('user')->whereDate('fecha', $hoy)->latest()->first();
        $montoInicialCaja = $cajaHoy ? (float) $cajaHoy->monto_inicial : 0.0;

        // Ventas del día
        $ventasHoy = Venta::whereDate('created_at', $hoy)->sum('total');
        $cantidadVentasHoy = Venta::whereDate('created_at', $hoy)->count();

        // Desglose de ventas del día por tipo de pago
        $ventasHoyPorForma = Venta::desglosePorPago(Venta::whereDate('created_at', $hoy));

        // Gastos (retiros) del día: salen del cajón, así que descuentan efectivo.
        $totalRetirosHoy = (float) Retiro::whereDate('created_at', $hoy)->sum('monto');

        // Total físico de efectivo en caja (Cambio inicial + ventas en efectivo − gastos)
        $totalEfectivoEnCaja = $montoInicialCaja + $ventasHoyPorForma['efectivo'] - $totalRetirosHoy;

        // Total del día: efectivo del cajón + lo cobrado por otros medios.
        // La cuenta corriente no entra: es plata a cobrar, todavía no cobrada.
        $totalCierreGeneral = $totalEfectivoEnCaja
            + array_sum($ventasHoyPorForma)
            - $ventasHoyPorForma['efectivo']
            - $ventasHoyPorForma['cuenta_corriente'];

        // Si el usuario es vendedor, mostrar vista específica de vendedor
        // con los datos de SU caja abierta (período en curso).
        if (Auth::user()?->isVendedor()) {
            $cajaHoy = Caja::abierta();
            $montoInicialCaja = $cajaHoy ? (float) $cajaHoy->monto_inicial : 0.0;

            $ventasCaja = $cajaHoy
                ? Venta::where('caja_id', $cajaHoy->id)->get()
                : collect();

            $totalRetirosCaja = $cajaHoy ? (float) $cajaHoy->retiros()->sum('monto') : 0.0;

            $ventasHoy = (float) $ventasCaja->sum('total');
            $cantidadVentasHoy = $ventasCaja->count();

            // Desglose de ventas de la caja por tipo de pago
            $ventasHoyPorForma = Venta::desglosePorPago(
                Venta::where('caja_id', $cajaHoy?->id ?? -1)
            );

            // Total físico de efectivo en caja (Cambio Inicial + Ventas en efectivo − Retiros)
            $totalEfectivoEnCaja = $montoInicialCaja + $ventasHoyPorForma['efectivo'] - $totalRetirosCaja;

            // Total del período: efectivo del cajón + lo cobrado por otros medios
            // (la cuenta corriente queda afuera: todavía no se cobró).
            $totalCierreGeneral = $totalEfectivoEnCaja
                + array_sum($ventasHoyPorForma)
                - $ventasHoyPorForma['efectivo']
                - $ventasHoyPorForma['cuenta_corriente'];

            $ventasHoyLista = $cajaHoy
                ? Venta::with('user')->where('caja_id', $cajaHoy->id)->latest()->get()
                : collect();

            $categoriasGasto = CategoriaGasto::orderBy('nombre')->get(['id', 'nombre']);

            return view('dashboard-vendedor', compact(
                'cajaHoy',
                'montoInicialCaja',
                'ventasHoy',
                'cantidadVentasHoy',
                'ventasHoyPorForma',
                'totalRetirosCaja',
                'totalEfectivoEnCaja',
                'totalCierreGeneral',
                'ventasHoyLista',
                'categoriasGasto'
            ));
        }

        // Total de productos distintos
        $totalProductos = Producto::count();

        // Valor total del stock (precio_compra * stock)
        $valorStock = (float) (Producto::selectRaw('SUM(precio_compra * stock) as total')->value('total') ?? 0);

        $inicioMes = Carbon::now()->startOfMonth();

        // Ventas acumuladas del mes actual
        $ventasMesActual = Venta::where('created_at', '>=', $inicioMes)->sum('total');

        // Ventas acumuladas del año actual
        $ventasAnoActual = Venta::where('created_at', '>=', Carbon::now()->startOfYear())->sum('total');

        // Desglose de ventas del mes actual por tipo de pago
        $ventasMesPorForma = Venta::desglosePorPago(Venta::where('created_at', '>=', $inicioMes));

        // Productos con stock bajo
        $productosBajoStock = Producto::stockBajo()
            ->with('categoria')
            ->orderBy('stock')
            ->get();

        // Últimas 5 ventas
        $ultimasVentas = Venta::with('user')
            ->latest()
            ->take(5)
            ->get();

        // Ranking de proveedores por cantidad comprada (últimos 12 meses)
        $haceUnAno = Carbon::now()->subMonths(12);

        $rankingProveedores = Proveedor::select('proveedores.id', 'proveedores.nombre')
            ->join('productos', 'productos.proveedor_id', '=', 'proveedores.id')
            ->where('productos.created_at', '>=', $haceUnAno)
            ->selectRaw('SUM(productos.stock) as total_unidades')
            ->selectRaw('SUM(productos.stock * productos.precio_compra) as total_inversion')
            ->selectRaw('COUNT(productos.id) as total_productos')
            ->groupBy('proveedores.id', 'proveedores.nombre')
            ->orderByDesc('total_unidades')
            ->take(10)
            ->get();

        // Descuentos otorgados (dinero perdido por descuentos)
        $descuentosHoy = Venta::whereDate('created_at', $hoy)
            ->where('monto_descuento', '>', 0)
            ->sum('monto_descuento');
        $cantidadDescuentosHoy = Venta::whereDate('created_at', $hoy)
            ->where('monto_descuento', '>', 0)
            ->count();

        $descuentosMes = Venta::where('created_at', '>=', $inicioMes)
            ->where('monto_descuento', '>', 0)
            ->sum('monto_descuento');
        $cantidadDescuentosMes = Venta::where('created_at', '>=', $inicioMes)
            ->where('monto_descuento', '>', 0)
            ->count();

        $descuentosAno = Venta::where('created_at', '>=', Carbon::now()->startOfYear())
            ->where('monto_descuento', '>', 0)
            ->sum('monto_descuento');
        $cantidadDescuentosAno = Venta::where('created_at', '>=', Carbon::now()->startOfYear())
            ->where('monto_descuento', '>', 0)
            ->count();

        return view('dashboard', compact(
            'cajaHoy',
            'montoInicialCaja',
            'valorStock',
            'ventasHoy',
            'cantidadVentasHoy',
            'ventasMesActual',
            'ventasAnoActual',
            'ventasHoyPorForma',
            'ventasMesPorForma',
            'totalRetirosHoy',
            'totalEfectivoEnCaja',
            'totalCierreGeneral',
            'productosBajoStock',
            'ultimasVentas',
            'rankingProveedores',
            'descuentosHoy',
            'cantidadDescuentosHoy',
            'descuentosMes',
            'cantidadDescuentosMes',
            'descuentosAno',
            'cantidadDescuentosAno'
        ));
    }
}
