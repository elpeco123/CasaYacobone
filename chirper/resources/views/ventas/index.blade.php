@extends('layouts.app')

@section('title', 'Ventas')

@push('styles')
<style>
    /* Panel de ventas compacto (PC y celular) */
    .ventas-compact .page-header { margin-bottom: 1rem; }
    .ventas-compact .page-header h1 { font-size: 1.3rem; margin-bottom: 0.25rem; }
    .ventas-compact .card-glass .card-body { padding: 0.9rem; }
    .ventas-compact .table-dark-custom thead th { padding: 0.55rem 0.7rem; font-size: 0.72rem; }
    .ventas-compact .table-dark-custom td { padding: 0.5rem 0.7rem; font-size: 0.83rem; }
    .ventas-compact .table-dark-custom .badge { font-size: 0.74rem; padding: 0.25rem 0.55rem; }
    .ventas-compact .table-dark-custom .btn-sm { padding: 0.25rem 0.55rem; font-size: 0.76rem; }
    .ventas-compact .venta-total { font-size: 0.95rem !important; }
</style>
@endpush

@section('content')
<div class="fade-in ventas-compact">
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h1><i class="bi bi-cart-fill me-2" style="color: var(--cy-gold);"></i>Ventas</h1>
            @if(!empty($soloHoy))
                @if(!empty($cajaAbierta))
                    <span class="badge" style="background: rgba(155, 179, 95, 0.15); color: #a9c46c; border: 1px solid rgba(155, 179, 95, 0.3); font-weight: 500;">
                        <i class="bi bi-unlock-fill me-1"></i>Caja #{{ $cajaAbierta->id }} · abierta {{ $cajaAbierta->fecha_apertura?->format('d/m H:i') }}
                    </span>
                @else
                    <a href="{{ route('caja.index') }}" class="badge text-decoration-none" style="background: rgba(224, 163, 58, 0.15); color: #fbbf24; border: 1px solid rgba(224, 163, 58, 0.3); font-weight: 500;">
                        <i class="bi bi-exclamation-circle-fill me-1"></i>Sin caja abierta · Tocá para abrir una
                    </a>
                @endif
            @endif
        </div>
        <a href="{{ route('ventas.create') }}" class="btn btn-accent btn-sm">
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
                            <th class="d-none d-md-table-cell">Fecha</th>
                            <th>Hora</th>
                            <th class="d-none d-md-table-cell">Vendedor</th>
                            <th class="text-center">Forma de Pago</th>
                            <th class="text-end">Total</th>
                            <th class="text-center">Ver</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ventas as $venta)
                        <tr>
                            <td class="fw-bold text-white">{{ $venta->id }}</td>
                            <td class="text-light d-none d-md-table-cell">{{ $venta->created_at->format('d/m/Y') }}</td>
                            <td style="color: #cdb99c;">{{ $venta->created_at->format('H:i') }}</td>
                            <td class="text-white d-none d-md-table-cell">
                                <i class="bi bi-person-fill me-1" style="color: var(--cy-gold);"></i>
                                {{ $venta->user->name }}
                            </td>
                            <td class="text-center">
                                @include('partials.chip-pago', ['tipo' => $venta->tipo_pago])
                                @if($venta->cliente)
                                    <div class="mt-1" style="font-size: 0.8rem; color: var(--cy-text-faint);">{{ $venta->cliente->nombreCompleto() }}</div>
                                @endif
                            </td>
                            <td class="text-end fw-bold venta-total" style="color: var(--cy-gold);">
                                ${{ number_format($venta->total, 0, ',', '.') }}
                            </td>
                            <td class="text-center">
                                <a href="{{ route('ventas.show', $venta) }}" class="btn btn-glass btn-sm text-light">
                                    <i class="bi bi-eye"></i><span class="d-none d-md-inline ms-1">Ver</span>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-3">
                                <i class="bi bi-cart-x" style="font-size: 1.8rem; color: #a8927a;"></i>
                                <p class="mt-2 mb-0" style="color: #cdb99c;">No hay ventas registradas.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($ventas->hasPages())
            <div class="d-flex justify-content-center mt-3">
                {{ $ventas->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
