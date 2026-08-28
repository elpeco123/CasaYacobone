<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoriaController extends Controller
{
    /**
     * Display a listing of the categorias.
     */
    public function index(): View
    {
        $categorias = Categoria::withCount('productos')->orderBy('nombre')->paginate(15);

        return view('categorias.index', compact('categorias'));
    }

    /**
     * Show the form for creating a new categoria.
     */
    public function create(): View
    {
        return view('categorias.create');
    }

    /**
     * Store a newly created categoria.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255', 'unique:categorias,nombre'],
        ], [
            'nombre.required' => 'El nombre de la categoría es obligatorio.',
            'nombre.unique' => 'Ya existe una categoría con ese nombre.',
        ]);

        Categoria::create($validated);

        return redirect()->route('categorias.index')
            ->with('success', 'Categoría creada correctamente.');
    }

    /**
     * Show the form for editing the specified categoria.
     */
    public function edit(Categoria $categoria): View
    {
        return view('categorias.edit', compact('categoria'));
    }

    /**
     * Update the specified categoria.
     */
    public function update(Request $request, Categoria $categoria): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255', 'unique:categorias,nombre,' . $categoria->id],
        ], [
            'nombre.required' => 'El nombre de la categoría es obligatorio.',
            'nombre.unique' => 'Ya existe una categoría con ese nombre.',
        ]);

        $categoria->update($validated);

        return redirect()->route('categorias.index')
            ->with('success', 'Categoría actualizada correctamente.');
    }

    /**
     * Remove the specified categoria.
     */
    public function destroy(Categoria $categoria): RedirectResponse
    {
        if ($categoria->productos()->count() > 0) {
            return redirect()->route('categorias.index')
                ->with('error', 'No se puede eliminar la categoría porque tiene productos asociados.');
        }

        $categoria->delete();

        return redirect()->route('categorias.index')
            ->with('success', 'Categoría eliminada correctamente.');
    }
}
