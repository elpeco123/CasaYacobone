@extends('layouts.app')

@section('title', 'Historial de Cajas')

@section('content')
<div class="fade-in">
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h1><i class="bi bi-safe2-fill me-2" style="color: var(--cy-gold);"></i>Historial de Cajas</h1>
            <p class="text-muted mb-0" style="font-size: 0.9rem;">Cada apertura y cierre con hora y totales por medio de pago</p>
        </div>
    </div>

    <div class="card-glass">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-dark-custom table-hover mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Responsable</th>
                            <th>Apertura</th>
                            <th>Cierre</th>
                            <th class="text-center">Estado</th>
                            <th class="text-end">Inicial</th>
                            <th class="text-end">💵 Efectivo</th>
                            <th class="text-end">➖ Gastos</th>
                            <th class="text-end">💳 Tarjeta</th>
                            <th class="text-end">📄 Factura</th>
                            <th class="text-center">Ventas</th>
                            <th class="text-end">Total</th>
                            <th class="text-end">Físico final</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cajas as $caja)
                            @php
                                // Las cerradas usan el snapshot; las abiertas, el cálculo en vivo.
                                $vivo = $enVivo[$caja->id] ?? null;
                                $efectivo = $vivo ? $vivo['efectivo'] : (float) $caja->total_efectivo;
                                $tarjeta = $vivo ? $vivo['tarjeta'] : (float) $caja->total_tarjeta;
                                $factura = $vivo ? $vivo['factura'] : (float) $caja->total_factura;
                                $retiros = $vivo ? $vivo['retiros'] : (float) $caja->total_retiros;
                                $cantidad = $vivo ? $vivo['cantidad'] : $caja->ventas_count;
                                $total = $efectivo + $tarjeta + $factura;
                                $fisico = (float) $caja->monto_inicial + $efectivo - $retiros;
                            @endphp
                            <tr>
                                <td class="fw-bold text-white">#{{ $caja->id }}</td>
                                <td class="text-white">
                                    <i class="bi bi-person-fill me-1" style="color: var(--cy-gold);"></i>
                                    {{ $caja->user->name ?? 'Usuario' }}
                                </td>
                                <td style="color: #cbd5e1;">
                                    {{ $caja->fecha_apertura?->format('d/m/Y H:i') ?? $caja->fecha?->format('d/m/Y') ?? '—' }}
                                </td>
                                <td style="color: #cbd5e1;">
                                    {{ $caja->fecha_cierre?->format('d/m/Y H:i') ?? '—' }}
                                </td>
                                <td class="text-center">
                                    @if($caja->estaAbierta())
                                        <span class="badge-stock-ok">
                                            <i class="bi bi-unlock-fill me-1"></i>Abierta
                                        </span>
                                    @else
                                        <span class="badge" style="background: rgba(148,163,184,0.15); color: #94a3b8; font-weight: 600; padding: 0.35rem 0.7rem; border-radius: 8px; font-size: 0.78rem;">
                                            <i class="bi bi-lock-fill me-1"></i>Cerrada
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end" style="color: #cbd5e1;">
                                    ${{ number_format($caja->monto_inicial, 0, ',', '.') }}
                                </td>
                                <td class="text-end fw-bold text-success">
                                    ${{ number_format($efectivo, 0, ',', '.') }}
                                </td>
                                <td class="text-end fw-bold text-danger">
                                    −${{ number_format($retiros, 0, ',', '.') }}
                                </td>
                                <td class="text-end fw-bold" style="color: #c084fc;">
                                    ${{ number_format($tarjeta, 0, ',', '.') }}
                                </td>
                                <td class="text-end fw-bold" style="color: #38bdf8;">
                                    ${{ number_format($factura, 0, ',', '.') }}
                                </td>
                                <td class="text-center">{{ $cantidad }}</td>
                                <td class="text-end fw-bold" style="font-size: 1.05rem; color: var(--cy-gold);">
                                    ${{ number_format($total, 0, ',', '.') }}
                                </td>
                                <td class="text-end fw-bold" style="color: #f6c078;">
                                    ${{ number_format($fisico, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="13" class="text-center py-4">
                                    <i class="bi bi-inbox" style="font-size: 2.2rem; color: #94a3b8;"></i>
                                    <p class="mt-2 mb-0" style="color: #cbd5e1;">Todavía no hay cajas registradas.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($cajas->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $cajas->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
