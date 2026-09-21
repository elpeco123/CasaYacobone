@extends('layouts.app')

@section('title', 'Categorías de Gasto')

@section('content')
<div class="fade-in">
    {{-- Page Header --}}
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h1><i class="bi bi-tags-fill me-2" style="color: var(--cy-gold);"></i>Categorías de Gasto</h1>
            <p class="text-muted mb-0" style="font-size: 0.9rem;">Impuestos, mantenimiento, varios… se usan al registrar gastos de caja</p>
        </div>
        <a href="{{ route('categoria-gastos.create') }}" class="btn btn-gold">
            <i class="bi bi-plus-circle-fill me-1"></i>Nueva Categoría
        </a>
    </div>

    {{-- Categorias Table --}}
    <div class="card-glass">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-dark-custom table-hover mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nombre</th>
                            <th class="text-center">Gastos</th>
                            <th class="text-center" style="width: 100px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categorias as $categoria)
                        <tr>
                            <td class="text-muted">{{ $categoria->id }}</td>
                            <td class="fw-bold">{{ $categoria->nombre }}</td>
                            <td class="text-center">
                                <span class="badge" style="background: rgba(143, 188, 212, 0.15); color: #8fbcd4; padding: 0.35rem 0.7rem; font-size: 0.78rem;">
                                    {{ $categoria->retiros_count }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex gap-1 justify-content-center">
                                    <a href="{{ route('categoria-gastos.edit', $categoria) }}" class="btn btn-glass btn-sm px-2 py-1" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('categoria-gastos.destroy', $categoria) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('¿Estás seguro de eliminar la categoría {{ $categoria->nombre }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm px-2 py-1"
                                                style="background: rgba(208, 85, 58, 0.15); border: 1px solid rgba(208, 85, 58, 0.3); color: #d0553a;"
                                                title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4">
                                <i class="bi bi-tags" style="font-size: 2rem; color: var(--cy-text-muted);"></i>
                                <p class="text-muted mt-2 mb-0">No hay categorías de gasto registradas.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($categorias->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $categorias->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
