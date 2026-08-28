<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\Venta;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with KPIs.
     */
    public function index(): View
    {
        $hoy = Carbon::today();

        // Total de productos distintos
        $totalProductos = Producto::count();

        // Valor total del stock (precio_compra * stock)
        $valorStock = Producto::selectRaw('SUM(precio_compra * stock) as total')->value('total') ?? 0;

        // Ventas acumuladas del mes actual
        $ventasMesActual = Venta::where('created_at', '>=', Carbon::now()->startOfMonth())->sum('total');

        // Ventas acumuladas del año actual
        $ventasAnoActual = Venta::where('created_at', '>=', Carbon::now()->startOfYear())->sum('total');

        // Ventas del día
        $ventasHoy = Venta::whereDate('created_at', $hoy)->sum('total');
        $cantidadVentasHoy = Venta::whereDate('created_at', $hoy)->count();

        // Desglose de ventas del día por tipo de pago
        $ventasHoyPorForma = [
            'efectivo' => Venta::whereDate('created_at', $hoy)->where('tipo_pago', 'efectivo')->sum('total'),
            'tarjeta' => Venta::whereDate('created_at', $hoy)->where('tipo_pago', 'tarjeta')->sum('total'),
            'factura' => Venta::whereDate('created_at', $hoy)->where('tipo_pago', 'factura')->sum('total'),
        ];

        // Desglose acumulado total por tipo de pago
        $ventasTotalesPorForma = [
            'efectivo' => Venta::where('tipo_pago', 'efectivo')->sum('total'),
            'tarjeta' => Venta::where('tipo_pago', 'tarjeta')->sum('total'),
            'factura' => Venta::where('tipo_pago', 'factura')->sum('total'),
            'total' => Venta::sum('total'),
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

        return view('dashboard', compact(
            'valorStock',
            'ventasHoy',
            'cantidadVentasHoy',
            'ventasMesActual',
            'ventasAnoActual',
            'ventasHoyPorForma',
            'ventasTotalesPorForma',
            'productosBajoStock',
            'ultimasVentas',
            'rankingProveedores'
        ));
    }
}
