@extends('layouts.app')

@section('title', 'Ventas')

@section('content')
<div class="fade-in">
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h1><i class="bi bi-cart-fill me-2" style="color: var(--cy-gold);"></i>Ventas</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Ventas</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('ventas.create') }}" class="btn btn-accent">
            <i class="bi bi-cart-plus-fill me-1"></i>Nueva Venta
        </a>
    </div>

    <div class="card-glass">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-dark-custom table-hover mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Vendedor</th>
                            <th class="text-end">Total</th>
                            <th class="text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ventas as $venta)
                        <tr>
                            <td class="fw-bold">{{ $venta->id }}</td>
                            <td>{{ $venta->created_at->format('d/m/Y') }}</td>
                            <td>{{ $venta->created_at->format('H:i') }}</td>
                            <td>
                                <i class="bi bi-person-fill me-1" style="color: var(--cy-text-muted);"></i>
                                {{ $venta->user->name }}
                            </td>
                            <td class="text-end fw-bold" style="font-size: 1.05rem; color: var(--cy-gold);">
                                ${{ number_format($venta->total, 0, ',', '.') }}
                            </td>
                            <td class="text-center">
                                <a href="{{ route('ventas.show', $venta) }}" class="btn btn-glass btn-sm">
                                    <i class="bi bi-eye me-1"></i>Ver Detalle
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <i class="bi bi-cart-x" style="font-size: 2rem; color: var(--cy-text-muted);"></i>
                                <p class="text-muted mt-2 mb-0">No hay ventas registradas.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($ventas->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $ventas->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
