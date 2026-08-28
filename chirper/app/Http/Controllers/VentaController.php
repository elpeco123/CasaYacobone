<?php

namespace App\Http\Controllers;

use App\Http\Requests\VentaRequest;
use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class VentaController extends Controller
{
    /**
     * Display a listing of ventas.
     */
    public function index(): View
    {
        $ventas = Venta::with('user')
            ->latest()
            ->paginate(15);

        return view('ventas.index', compact('ventas'));
    }

    /**
     * Show the form for creating a new venta.
     */
    public function create(): View
    {
        $productos = Producto::with('categoria')
            ->where('stock', '>', 0)
            ->orderBy('nombre')
            ->get();

        return view('ventas.create', compact('productos'));
    }

    /**
     * Store a newly created venta with stock discount inside a DB transaction.
     */
    public function store(VentaRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        try {
            $venta = DB::transaction(function () use ($validated) {
                $total = 0;
                $itemsData = [];

                foreach ($validated['items'] as $item) {
                    $producto = Producto::lockForUpdate()->findOrFail($item['producto_id']);

                    // Validate stock availability
                    if ($producto->stock < $item['cantidad']) {
                        throw new \Exception(
                            "Stock insuficiente para \"{$producto->nombre}\". ".
                            "Disponible: {$producto->stock}, Solicitado: {$item['cantidad']}."
                        );
                    }

                    $subtotal = $producto->precio_venta * $item['cantidad'];
                    $total += $subtotal;

                    $itemsData[] = [
                        'producto_id' => $producto->id,
                        'cantidad' => $item['cantidad'],
                        'precio_compra' => $producto->precio_compra,
                        'precio_unitario' => $producto->precio_venta,
                        'subtotal' => $subtotal,
                    ];

                    // Discount stock
                    $producto->decrement('stock', $item['cantidad']);
                }

                // Create the venta
                $venta = Venta::create([
                    'user_id' => Auth::id(),
                    'tipo_pago' => $validated['tipo_pago'],
                    'total' => $total,
                ]);

                // Create the items
                foreach ($itemsData as $itemData) {
                    $venta->items()->create($itemData);
                }

                return $venta;
            });

            return redirect()->route('ventas.show', $venta)
                ->with('success', 'Venta registrada correctamente. Total: $'.number_format($venta->total, 2, ',', '.'));

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified venta.
     */
    public function show(Venta $venta): View
    {
        $venta->load(['user', 'items.producto.categoria']);

        return view('ventas.show', compact('venta'));
    }
}
