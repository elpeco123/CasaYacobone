<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Venta;
use App\Models\VentaItem;
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

        // Ventas del día
        $ventasHoy = Venta::whereDate('created_at', $hoy)->sum('total');
        $cantidadVentasHoy = Venta::whereDate('created_at', $hoy)->count();

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

        return view('dashboard', compact(
            'totalProductos',
            'valorStock',
            'ventasHoy',
            'cantidadVentasHoy',
            'productosBajoStock',
            'ultimasVentas'
        ));
    }
}
