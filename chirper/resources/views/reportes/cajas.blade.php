@extends('layouts.app')

@section('title', 'Historial de Cajas')

@push('styles')
<style>
    /* Evita que fechas y montos se partan en dos líneas */
    #tabla-cajas th,
    #tabla-cajas td {
        white-space: nowrap;
        vertical-align: middle;
        padding: 0.45rem 0.5rem;
        font-size: 0.78rem;
    }

    #tabla-cajas th {
        font-size: 0.74rem;
    }

    /* El total del período es la columna que se busca primero. */
    #tabla-cajas td.col-total {
        font-size: 0.92rem;
    }
</style>
@endpush

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
                <table id="tabla-cajas" class="table table-dark-custom table-hover mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th class="d-none d-xxl-table-cell">Abrió</th>
                            <th>Apertura</th>
                            <th>Cierre</th>
                            <th class="text-center">Estado</th>
                            <th class="text-end d-none d-xxl-table-cell" title="Cambio inicial de la apertura">Cambio</th>
                            <th class="text-end">Efectivo</th>
                            <th class="text-end">Gastos</th>
                            <th class="text-end" title="Incluye las ventas viejas cargadas como tarjeta">Débito</th>
                            <th class="text-end">Crédito</th>
                            <th class="text-end">Factura</th>
                            <th class="text-end" title="Ventas a cuenta corriente: quedan a cobrar">Cta. cte.</th>
                            <th class="text-center">Ventas</th>
                            <th class="text-end">Total</th>
                            <th class="text-end" title="Efectivo que tiene que haber en el cajón">Físico</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cajas as $caja)
                            @php
                                // Las cerradas usan el snapshot; las abiertas, el cálculo en vivo.
                                $vivo = $enVivo[$caja->id] ?? null;
                                $efectivo = $vivo ? $vivo['efectivo'] : (float) $caja->total_efectivo;
                                // "Tarjeta" existe solo en cajas viejas, antes de separar débito y crédito.
                                $tarjeta = $vivo ? ($vivo['tarjeta'] ?? 0) : (float) $caja->total_tarjeta;
                                $debito = $vivo ? $vivo['debito'] : (float) $caja->total_debito;
                                $credito = $vivo ? $vivo['credito'] : (float) $caja->total_credito;
                                $factura = $vivo ? $vivo['factura'] : (float) $caja->total_factura;
                                $cuentaCorriente = $vivo ? $vivo['cuenta_corriente'] : (float) $caja->total_cuenta_corriente;
                                $retiros = $vivo ? $vivo['retiros'] : (float) $caja->total_retiros;
                                $cantidad = $vivo ? $vivo['cantidad'] : $caja->ventas_count;
                                $total = $efectivo + $tarjeta + $debito + $credito + $factura + $cuentaCorriente;
                                $fisico = (float) $caja->monto_inicial + $efectivo - $retiros;
                            @endphp
                            <tr>
                                <td class="fw-bold text-white">#{{ $caja->id }}</td>
                                <td class="text-white d-none d-xxl-table-cell">{{ $caja->user->name ?? 'Usuario' }}</td>
                                <td style="color: #cdb99c;">
                                    {{ $caja->fecha_apertura?->format('d/m/y H:i') ?? $caja->fecha?->format('d/m/y') ?? '—' }}
                                </td>
                                <td style="color: #cdb99c;">
                                    {{ $caja->fecha_cierre?->format('d/m/y H:i') ?? '—' }}
                                </td>
                                <td class="text-center">
                                    @if($caja->estaAbierta())
                                        <span class="badge-stock-ok">
                                            <i class="bi bi-unlock-fill me-1"></i>Abierta
                                        </span>
                                    @else
                                        <span class="badge" style="background: rgba(168, 146, 122, 0.15); color: #a8927a; font-weight: 600; padding: 0.35rem 0.7rem; border-radius: 8px; font-size: 0.78rem;">
                                            <i class="bi bi-lock-fill me-1"></i>Cerrada
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end d-none d-xxl-table-cell" style="color: #cdb99c;">
                                    ${{ number_format($caja->monto_inicial, 0, ',', '.') }}
                                </td>
                                <td class="text-end fw-bold text-success">
                                    ${{ number_format($efectivo, 0, ',', '.') }}
                                </td>
                                <td class="text-end fw-bold text-danger">
                                    −${{ number_format($retiros, 0, ',', '.') }}
                                </td>
                                <td class="text-end fw-bold" style="color: #cfa0c6;">
                                    ${{ number_format($debito + $tarjeta, 0, ',', '.') }}
                                </td>
                                <td class="text-end fw-bold" style="color: #cfa0c6;">
                                    ${{ number_format($credito, 0, ',', '.') }}
                                </td>
                                <td class="text-end fw-bold" style="color: #8fbcd4;">
                                    ${{ number_format($factura, 0, ',', '.') }}
                                </td>
                                <td class="text-end fw-bold" style="color: #ecc58f;">
                                    ${{ number_format($cuentaCorriente, 0, ',', '.') }}
                                </td>
                                <td class="text-center">{{ $cantidad }}</td>
                                <td class="text-end fw-bold col-total" style="color: var(--cy-gold);">
                                    ${{ number_format($total, 0, ',', '.') }}
                                </td>
                                <td class="text-end fw-bold" style="color: #f6c078;">
                                    ${{ number_format($fisico, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="15" class="text-center py-4">
                                    <i class="bi bi-inbox" style="font-size: 2.2rem; color: #a8927a;"></i>
                                    <p class="mt-2 mb-0" style="color: #cdb99c;">Todavía no hay cajas registradas.</p>
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
