<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProveedorController extends Controller
{
    /**
     * Display a listing of the proveedores.
     */
    public function index(): View
    {
        $proveedores = Proveedor::withCount('productos')->orderBy('nombre')->paginate(15);

        return view('proveedores.index', compact('proveedores'));
    }

    /**
     * Show the form for creating a new proveedor.
     */
    public function create(): View
    {
        return view('proveedores.create');
    }

    /**
     * Store a newly created proveedor.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255', 'unique:proveedores,nombre'],
            'telefono' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
        ], [
            'nombre.required' => 'El nombre del proveedor es obligatorio.',
            'nombre.unique' => 'Ya existe un proveedor con ese nombre.',
            'email.email' => 'El email debe ser una dirección válida.',
        ]);

        Proveedor::create($validated);

        return redirect()->route('proveedores.index')
            ->with('success', 'Proveedor creado correctamente.');
    }

    /**
     * Show the form for editing the specified proveedor.
     */
    public function edit(Proveedor $proveedor): View
    {
        return view('proveedores.edit', compact('proveedor'));
    }

    /**
     * Update the specified proveedor.
     */
    public function update(Request $request, Proveedor $proveedor): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255', 'unique:proveedores,nombre,' . $proveedor->id],
            'telefono' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
        ], [
            'nombre.required' => 'El nombre del proveedor es obligatorio.',
            'nombre.unique' => 'Ya existe un proveedor con ese nombre.',
            'email.email' => 'El email debe ser una dirección válida.',
        ]);

        $proveedor->update($validated);

        return redirect()->route('proveedores.index')
            ->with('success', 'Proveedor actualizado correctamente.');
    }

    /**
     * Remove the specified proveedor.
     */
    public function destroy(Proveedor $proveedor): RedirectResponse
    {
        if ($proveedor->productos()->count() > 0) {
            return redirect()->route('proveedores.index')
                ->with('error', 'No se puede eliminar el proveedor porque tiene productos asociados.');
        }

        $proveedor->delete();

        return redirect()->route('proveedores.index')
            ->with('success', 'Proveedor eliminado correctamente.');
    }
}
