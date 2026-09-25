<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductoRequest;
use App\Models\Categoria;
use App\Models\Producto;
use App\Models\Proveedor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductoController extends Controller
{
    /**
     * Display a listing of the productos.
     */
    public function index(Request $request): View
    {
        $query = Producto::with('categoria', 'proveedor');

        // Búsqueda
        if ($request->filled('buscar')) {
            $buscar = $request->input('buscar');
            $query->where(function ($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                    ->orWhere('articulo', 'like', "%{$buscar}%")
                    ->orWhere('color', 'like', "%{$buscar}%");
            });
        }

        // Filtro por categoría
        if ($request->filled('categoria_id')) {
            $query->where('categoria_id', $request->input('categoria_id'));
        }

        // Filtro stock bajo
        if ($request->boolean('stock_bajo')) {
            $query->stockBajo();
        }

        $totalProductosCount = Producto::count();
        $totalStockUnidades = (int) Producto::sum('stock');
        $valorTotalStockCompra = (float) (Producto::selectRaw('SUM(precio_compra * stock) as total')->value('total') ?? 0);
        $valorTotalStockVenta = (float) (Producto::selectRaw('SUM(precio_venta * stock) as total')->value('total') ?? 0);

        // Las variantes del mismo artículo quedan juntas.
        $productos = $query->orderBy('articulo')->orderBy('talle')->orderBy('color')->paginate(15)->withQueryString();
        $categorias = Categoria::orderBy('nombre')->get();

        return view('productos.index', compact(
            'productos',
            'categorias',
            'totalProductosCount',
            'totalStockUnidades',
            'valorTotalStockCompra',
            'valorTotalStockVenta'
        ));
    }

    /**
     * Show the form for creating a new producto.
     */
    public function create(): View
    {
        $categorias = Categoria::orderBy('nombre')->get();
        $proveedores = Proveedor::orderBy('nombre')->get();
        $articulosUsados = $this->articulosUsados();

        return view('productos.create', compact('categorias', 'proveedores', 'articulosUsados'));
    }

    /**
     * Códigos de artículo ya cargados, para autocompletar el formulario.
     *
     * @return array<int, string>
     */
    private function articulosUsados(): array
    {
        return Producto::query()->distinct()->orderBy('articulo')->pluck('articulo')->all();
    }

    /**
     * Store a newly created producto.
     */
    public function store(ProductoRequest $request): RedirectResponse
    {
        Producto::create($request->validated());

        return redirect()->route('productos.index')
            ->with('success', 'Producto creado correctamente.');
    }

    /**
     * Display the specified producto.
     */
    public function show(Producto $producto): View
    {
        $producto->load('categoria', 'proveedor');

        return view('productos.show', compact('producto'));
    }

    /**
     * Show the form for editing the specified producto.
     */
    public function edit(Producto $producto): View
    {
        $categorias = Categoria::orderBy('nombre')->get();
        $proveedores = Proveedor::orderBy('nombre')->get();
        $articulosUsados = $this->articulosUsados();

        return view('productos.edit', compact('producto', 'categorias', 'proveedores', 'articulosUsados'));
    }

    /**
     * Update the specified producto.
     */
    public function update(ProductoRequest $request, Producto $producto): RedirectResponse
    {
        $producto->update($request->validated());

        return redirect()->route('productos.index')
            ->with('success', 'Producto actualizado correctamente.');
    }

    /**
     * Remove the specified producto (admin only).
     */
    public function destroy(Producto $producto): RedirectResponse
    {
        $producto->delete();

        return redirect()->route('productos.index')
            ->with('success', 'Producto eliminado correctamente.');
    }
}
