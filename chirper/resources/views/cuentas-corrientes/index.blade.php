@extends('layouts.app')

@section('title', 'Cuentas corrientes')

@section('content')
<div class="fade-in">
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h1 class="mb-0">Cuentas corrientes</h1>
            <p class="text-muted mb-0" style="font-size: 0.9rem;">
                Clientes que se llevaron mercadería y pagan después. Se cobra cada 30 días.
            </p>
        </div>
        <div class="d-flex gap-2">
            <div class="text-end">
                <div class="text-muted" style="font-size: 0.78rem;">Total a cobrar</div>
                <div class="fw-bold" style="font-size: 1.35rem; color: var(--cy-gold); font-family: var(--cy-font-display);">
                    ${{ number_format($totalAdeudado, 0, ',', '.') }}
                </div>
            </div>
        </div>
    </div>

    @if($vencidos > 0)
        <div class="alert alert-custom-warning" role="alert">
            <i class="bi bi-clock-history me-2"></i>
            {{ $vencidos }} {{ $vencidos === 1 ? 'cliente lleva' : 'clientes llevan' }} 30 días o más sin pagar.
        </div>
    @endif

    <div class="card-glass">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                <h5 class="mb-0" style="font-weight: 700;">
                    {{ $soloDeudores ? 'Con deuda' : 'Todos los clientes' }}
                    <span class="text-muted" style="font-size: 0.9rem; font-weight: 500;">· {{ $clientes->count() }}</span>
                </h5>
                <div class="btn-group btn-group-sm" role="group">
                    <a href="{{ route('cuentas-corrientes.index') }}"
                       class="btn {{ $soloDeudores ? 'btn-gold' : 'btn-glass' }}">Con deuda</a>
                    <a href="{{ route('cuentas-corrientes.index', ['deudores' => 0]) }}"
                       class="btn {{ $soloDeudores ? 'btn-glass' : 'btn-gold' }}">Todos</a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-dark-custom table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Teléfono</th>
                            <th>Dirección</th>
                            <th class="text-center">Debe desde</th>
                            <th class="text-end">Saldo</th>
                            <th class="text-end">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clientes as $cliente)
                            <tr>
                                <td class="text-white fw-bold">{{ $cliente->nombreCompleto() }}</td>
                                <td style="color: #cdb99c;">{{ $cliente->telefono }}</td>
                                <td style="color: #cdb99c;">{{ $cliente->direccion }}</td>
                                <td class="text-center">
                                    @if($cliente->desde)
                                        <span class="{{ $cliente->diasDeuda >= 30 ? 'badge-stock-critico' : 'badge-stock-ok' }}">
                                            {{ $cliente->desde->format('d/m/Y') }} · {{ $cliente->diasDeuda }} días
                                        </span>
                                    @else
                                        <span class="text-muted">Al día</span>
                                    @endif
                                </td>
                                <td class="text-end fw-bold" style="color: {{ $cliente->saldoActual > 0 ? 'var(--cy-gold)' : '#a9c46c' }};">
                                    ${{ number_format($cliente->saldoActual, 0, ',', '.') }}
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('cuentas-corrientes.show', $cliente) }}" class="btn btn-glass btn-sm">
                                        Ver y cobrar
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    {{ $soloDeudores ? 'Nadie debe nada. Todas las cuentas están al día.' : 'Todavía no hay clientes con cuenta corriente.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
