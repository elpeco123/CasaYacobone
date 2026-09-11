<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\CategoriaGasto;
use App\Models\Retiro;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RetiroController extends Controller
{
    /**
     * Registra un gasto (retiro) de la caja abierta: monto + categoría + detalle.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'monto' => ['required', 'numeric', 'min:1', 'max:999999999'],
            'categoria_gasto_id' => ['required', 'exists:categoria_gastos,id'],
            'concepto' => ['nullable', 'string', 'max:255'],
        ], [
            'monto.required' => 'Ingresá el monto gastado.',
            'monto.numeric' => 'El monto debe ser un número válido.',
            'monto.min' => 'El monto debe ser mayor a 0.',
            'categoria_gasto_id.required' => 'Elegí la categoría del gasto.',
            'categoria_gasto_id.exists' => 'La categoría elegida no existe.',
        ]);

        $cajaAbierta = Caja::abierta();
        if (! $cajaAbierta) {
            return redirect()->route('caja.index')
                ->with('error', 'No hay ninguna caja abierta para registrar el gasto.');
        }

        $categoria = CategoriaGasto::find($validated['categoria_gasto_id']);

        Retiro::create([
            'caja_id' => $cajaAbierta->id,
            'user_id' => Auth::id(),
            'categoria_gasto_id' => $validated['categoria_gasto_id'],
            'monto' => $validated['monto'],
            // Si no hay detalle, se guarda la categoría como concepto.
            'concepto' => $validated['concepto'] ?: $categoria->nombre,
        ]);

        return redirect()->route('caja.index')
            ->with('success', 'Gasto de $'.number_format($validated['monto'], 0, ',', '.').' ('.$categoria->nombre.') descontado de la caja #'.$cajaAbierta->id.'.');
    }

    /**
     * Elimina un retiro cargado por error (solo con la caja abierta).
     */
    public function destroy(Retiro $retiro): RedirectResponse
    {
        if ($retiro->user_id !== Auth::id() && ! Auth::user()->isAdmin()) {
            abort(403, 'No tenés permiso para eliminar este gasto.');
        }

        if (! $retiro->caja->estaAbierta()) {
            return redirect()->route('caja.index')
                ->with('error', 'No se puede eliminar un gasto de una caja ya cerrada.');
        }

        $retiro->delete();

        return redirect()->route('caja.index')
            ->with('success', 'Gasto eliminado y monto devuelto a la caja.');
    }
}
