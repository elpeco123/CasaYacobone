<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\Producto;
use App\Models\Proveedor;
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
        $ventasHoyPorForma = [
            'efectivo' => Venta::whereDate('created_at', $hoy)->where('tipo_pago', 'efectivo')->sum('total'),
            'tarjeta' => Venta::whereDate('created_at', $hoy)->where('tipo_pago', 'tarjeta')->sum('total'),
            'factura' => Venta::whereDate('created_at', $hoy)->where('tipo_pago', 'factura')->sum('total'),
        ];

        // Total físico de efectivo en caja (Cambio Inicial + Ventas en efectivo)
        $totalEfectivoEnCaja = $montoInicialCaja + $ventasHoyPorForma['efectivo'];

        // Total Cierre de Caja General
        $totalCierreGeneral = $totalEfectivoEnCaja + $ventasHoyPorForma['tarjeta'] + $ventasHoyPorForma['factura'];

        // Si el usuario es vendedor, mostrar vista específica de vendedor
        if (Auth::user()?->isVendedor()) {
            $ventasHoyLista = Venta::with('user')
                ->whereDate('created_at', $hoy)
                ->latest()
                ->get();

            return view('dashboard-vendedor', compact(
                'cajaHoy',
                'montoInicialCaja',
                'ventasHoy',
                'cantidadVentasHoy',
                'ventasHoyPorForma',
                'totalEfectivoEnCaja',
                'totalCierreGeneral',
                'ventasHoyLista'
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
        $ventasMesPorForma = [
            'efectivo' => Venta::where('created_at', '>=', $inicioMes)->where('tipo_pago', 'efectivo')->sum('total'),
            'tarjeta' => Venta::where('created_at', '>=', $inicioMes)->where('tipo_pago', 'tarjeta')->sum('total'),
            'factura' => Venta::where('created_at', '>=', $inicioMes)->where('tipo_pago', 'factura')->sum('total'),
            'total' => $ventasMesActual,
        ];

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
