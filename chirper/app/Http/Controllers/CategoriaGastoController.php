<?php

namespace App\Http\Controllers;

use App\Models\CategoriaGasto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoriaGastoController extends Controller
{
    /**
     * Display a listing of categorias de gasto.
     */
    public function index(): View
    {
        $categorias = CategoriaGasto::withCount('retiros')->orderBy('nombre')->paginate(15);

        return view('categoria-gastos.index', compact('categorias'));
    }

    /**
     * Show the form for creating a new categoria de gasto.
     */
    public function create(): View
    {
        return view('categoria-gastos.create');
    }

    /**
     * Store a newly created categoria de gasto.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:100', 'unique:categoria_gastos,nombre'],
        ], [
            'nombre.required' => 'El nombre de la categoría es obligatorio.',
            'nombre.unique' => 'Ya existe una categoría de gasto con ese nombre.',
        ]);

        CategoriaGasto::create($validated);

        return redirect()->route('categoria-gastos.index')
            ->with('success', 'Categoría de gasto creada correctamente.');
    }

    /**
     * Show the form for editing the specified categoria de gasto.
     */
    public function edit(CategoriaGasto $categoriaGasto): View
    {
        return view('categoria-gastos.edit', ['categoria' => $categoriaGasto]);
    }

    /**
     * Update the specified categoria de gasto.
     */
    public function update(Request $request, CategoriaGasto $categoriaGasto): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:100', 'unique:categoria_gastos,nombre,'.$categoriaGasto->id],
        ], [
            'nombre.required' => 'El nombre de la categoría es obligatorio.',
            'nombre.unique' => 'Ya existe una categoría de gasto con ese nombre.',
        ]);

        $categoriaGasto->update($validated);

        return redirect()->route('categoria-gastos.index')
            ->with('success', 'Categoría de gasto actualizada correctamente.');
    }

    /**
     * Remove the specified categoria de gasto.
     */
    public function destroy(CategoriaGasto $categoriaGasto): RedirectResponse
    {
        if ($categoriaGasto->retiros()->count() > 0) {
            return redirect()->route('categoria-gastos.index')
                ->with('error', 'No se puede eliminar la categoría porque tiene gastos asociados.');
        }

        $categoriaGasto->delete();

        return redirect()->route('categoria-gastos.index')
            ->with('success', 'Categoría de gasto eliminada correctamente.');
    }
}
