@extends('layouts.app')

@section('title', 'Panel de Vendedor')

@push('styles')
<style>
    /* Ajustes específicos para teléfonos celulares */
    @media (max-width: 767.98px) {
        .vendedor-header-title {
            font-size: 1.5rem !important;
        }
        .vendedor-hero-card {
            padding: 1.25rem !important;
        }
        .vendedor-hero-title {
            font-size: 1.3rem !important;
        }
        .cierre-total-amount {
            font-size: 1.35rem !important;
        }
        .sale-mobile-card {
            background: rgba(15, 23, 42, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            padding: 0.9rem;
            margin-bottom: 0.75rem;
            transition: background 0.2s ease;
        }
        .sale-mobile-card:active {
            background: rgba(212, 165, 116, 0.15);
        }
    }
</style>
@endpush

@section('content')
<div class="fade-in">
    {{-- Page Header --}}
    <div class="page-header d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 mb-4">
        <div>
            <h1 class="vendedor-header-title mb-1" style="color: #ffffff; font-weight: 800;">
                <i class="bi bi-person-badge-fill me-2" style="color: var(--cy-gold);"></i>Panel de Vendedor
            </h1>
            <p class="mb-0" style="font-size: 0.92rem; color: #cbd5e1;">
                Bienvenido/a, <strong style="color: #f8f9fa;">{{ Auth::user()->name }}</strong> · Casa Yacobone
            </p>
        </div>
        <div>
            <span class="badge px-3 py-2" style="font-size: 0.85rem; color: #f8f9fa; background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); font-weight: 500;">
                <i class="bi bi-calendar3 me-1" style="color: var(--cy-gold);"></i>{{ now()->locale('es')->isoFormat('dddd, D [de] MMMM') }}
            </span>
        </div>
    </div>

    {{-- Main Action Hero Card: Registrar Venta --}}
    <div class="card-glass vendedor-hero-card p-4 mb-4" style="background: linear-gradient(135deg, rgba(212, 165, 116, 0.22), rgba(22, 33, 62, 0.9)); border: 1px solid rgba(212, 165, 116, 0.45); border-radius: 16px;">
        <div class="row align-items-center g-3">
            <div class="col-md-8 text-center text-md-start">
                <h2 class="vendedor-hero-title mb-2" style="font-weight: 800; color: #ffffff;">
                    <i class="bi bi-cart-plus-fill me-2" style="color: var(--cy-gold);"></i>Registrar Nueva Venta
                </h2>
                <p class="mb-0" style="font-size: 0.95rem; color: #e2e8f0; font-weight: 400;">
                    Cobro rápido de productos, selección de forma de pago y emisión de comprobante.
                </p>
            </div>
            <div class="col-md-4 text-center text-md-end">
                <a href="{{ route('ventas.create') }}" class="btn btn-gold btn-lg px-4 py-3 fw-bold w-100 shadow d-inline-flex justify-content-center align-items-center" style="font-size: 1.1rem; color: #0f0f1e; background: linear-gradient(135deg, #d4a574, #e8c39e); border: none; border-radius: 12px; min-height: 52px;">
                    <i class="bi bi-plus-circle-fill me-2" style="font-size: 1.25rem;"></i>Nueva Venta
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Resumen Cierre de Caja del Día --}}
        <div class="col-lg-5">
            <div class="card-glass h-100" style="border-radius: 16px; border: 1px solid var(--cy-border);">
                <div class="card-body d-flex flex-column justify-content-between p-3 p-md-4">
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="mb-0" style="font-weight: 800; color: #ffffff;">
                                <i class="bi bi-wallet2 me-2" style="color: var(--cy-gold);"></i>
                                Cierre de Caja del Día
                            </h5>
                            <a href="{{ route('caja.index') }}" class="btn btn-gold btn-sm py-1 px-2.5 fw-bold" style="font-size: 0.78rem;">
                                <i class="bi bi-cash-stack me-1"></i>{{ $cajaHoy ? 'Editar Cambio' : 'Abrir Caja' }}
                            </a>
                        </div>
                        <p class="mb-3" style="font-size: 0.85rem; color: #cbd5e1;">
                            Resumen detallado de efectivo físico en caja y cobros electrónicos del día.
                        </p>

                        <div class="p-3 mb-3" style="background: rgba(15, 23, 42, 0.6); border-radius: 12px; border: 1px solid rgba(255, 255, 255, 0.1);">
                            {{-- Cambio Inicial --}}
                            <div class="d-flex justify-content-between align-items-center py-1.5" style="border-bottom: 1px dashed rgba(255, 255, 255, 0.1); font-size: 0.92rem;">
                                <span style="color: #cbd5e1; font-weight: 500;">🪙 Cambio Inicial (Apertura):</span>
                                <span class="fw-bold" style="color: var(--cy-gold); font-size: 1.05rem;">${{ number_format($montoInicialCaja, 0, ',', '.') }}</span>
                            </div>

                            {{-- Ventas en Efectivo --}}
                            <div class="d-flex justify-content-between align-items-center py-1.5" style="border-bottom: 1px dashed rgba(255, 255, 255, 0.1); font-size: 0.92rem;">
                                <span style="color: #cbd5e1; font-weight: 500;">💵 Ventas en Efectivo:</span>
                                <span class="fw-bold" style="color: #4ade80; font-size: 1.05rem;">+${{ number_format($ventasHoyPorForma['efectivo'], 0, ',', '.') }}</span>
                            </div>

                            {{-- TOTAL EFECTIVO FÍSICO EN CAJA --}}
                            <div class="d-flex justify-content-between align-items-center py-2 px-2 my-1.5 rounded-2" style="background: rgba(212, 165, 116, 0.15); border: 1px solid rgba(212, 165, 116, 0.35); font-size: 0.95rem;">
                                <span style="color: #ffffff; font-weight: 700;">💰 Total Efectivo en Caja:</span>
                                <span class="fw-extrabold" style="color: #f6c078; font-size: 1.25rem;">${{ number_format($totalEfectivoEnCaja, 0, ',', '.') }}</span>
                            </div>

                            {{-- Tarjeta y Factura --}}
                            <div class="d-flex justify-content-between align-items-center py-1.5" style="border-bottom: 1px dashed rgba(255, 255, 255, 0.1); font-size: 0.92rem;">
                                <span style="color: #cbd5e1; font-weight: 500;">💳 Tarjeta:</span>
                                <span class="fw-bold" style="font-size: 1.05rem; color: #c084fc;">${{ number_format($ventasHoyPorForma['tarjeta'], 0, ',', '.') }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center py-1.5" style="font-size: 0.92rem;">
                                <span style="color: #cbd5e1; font-weight: 500;">📄 Factura:</span>
                                <span class="fw-bold" style="font-size: 1.05rem; color: #38bdf8;">${{ number_format($ventasHoyPorForma['factura'], 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <div class="p-3" style="background: rgba(212, 165, 116, 0.12); border-radius: 12px; border: 1px solid rgba(212, 165, 116, 0.35);">
                            <div class="mb-1" style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.6px; font-weight: 700; color: #e2e8f0;">
                                Total General del Día (Efectivo en Caja + Digital)
                            </div>
                            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2">
                                <div>
                                    <div class="fw-bold cierre-total-amount" style="font-size: 1.5rem; color: var(--cy-gold); text-shadow: 0 0 10px rgba(212, 165, 116, 0.2);">
                                        ${{ number_format($totalCierreGeneral, 0, ',', '.') }}
                                    </div>
                                    <div style="font-size: 0.82rem; color: #cbd5e1; font-weight: 500;">
                                        {{ $cantidadVentasHoy }} {{ $cantidadVentasHoy === 1 ? 'operación' : 'operaciones' }} hoy · Ventas: ${{ number_format($ventasHoy, 0, ',', '.') }}
                                    </div>
                                </div>
                                <div>
                                    @if($cajaHoy)
                                        <span class="badge bg-success bg-opacity-25 text-success border border-success px-3 py-2" style="font-size: 0.82rem; font-weight: 700; color: #4ade80 !important;">
                                            <i class="bi bi-check-circle-fill me-1"></i>Caja Abierta
                                        </span>
                                    @else
                                        <a href="{{ route('caja.index') }}" class="badge bg-warning bg-opacity-25 text-warning border border-warning px-3 py-2 text-decoration-none" style="font-size: 0.82rem; font-weight: 700; color: #fbbf24 !important;">
                                            <i class="bi bi-exclamation-circle-fill me-1"></i>Abrir Caja
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 pt-2 text-center" style="font-size: 0.8rem; color: #94a3b8;">
                        <i class="bi bi-info-circle me-1" style="color: var(--cy-gold);"></i>El <strong>Total Efectivo en Caja (${{ number_format($totalEfectivoEnCaja, 0, ',', '.') }})</strong> es el monto físico que debe contarse en el arqueo al cerrar el día.
                    </div>
                </div>
            </div>
        </div>

        {{-- Historial de Ventas del Día --}}
        <div class="col-lg-7">
            <div class="card-glass h-100" style="border-radius: 16px; border: 1px solid var(--cy-border);">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0" style="font-weight: 800; color: #ffffff;">
                            <i class="bi bi-clock-history me-2" style="color: var(--cy-accent);"></i>
                            Historial de Ventas del Día
                        </h5>
                        <span class="badge px-3 py-2" style="font-size: 0.85rem; background: rgba(255, 255, 255, 0.1); color: #f8f9fa; border: 1px solid rgba(255, 255, 255, 0.2); font-weight: 600;">
                            {{ $ventasHoyLista->count() }} registradas
                        </span>
                    </div>

                    @if($ventasHoyLista->isEmpty())
                        <div class="text-center py-5">
                            <i class="bi bi-receipt" style="font-size: 3rem; color: #64748b;"></i>
                            <p class="mt-3 mb-1 fw-bold" style="color: #f8f9fa; font-size: 1.05rem;">Aún no hay ventas registradas hoy</p>
                            <p class="small mb-3" style="color: #cbd5e1;">Presione el botón "Nueva Venta" para realizar la primera transacción.</p>
                            <a href="{{ route('ventas.create') }}" class="btn btn-gold btn-sm px-3 py-2 fw-bold" style="color: #0f0f1e;">
                                <i class="bi bi-plus-circle me-1"></i>Registrar Venta
                            </a>
                        </div>
                    @else
                        {{-- Vista Desktop (Tabla) --}}
                        <div class="d-none d-md-block table-responsive" style="max-height: 420px; overflow-y: auto;">
                            <table class="table table-dark-custom table-hover align-middle mb-0">
                                <thead style="position: sticky; top: 0; background: #16213e; z-index: 10; border-bottom: 2px solid var(--cy-border);">
                                    <tr>
                                        <th style="color: #f8f9fa; font-weight: 700;">Venta #</th>
                                        <th style="color: #f8f9fa; font-weight: 700;">Hora</th>
                                        <th style="color: #f8f9fa; font-weight: 700;">Forma de Pago</th>
                                        <th class="text-end" style="color: #f8f9fa; font-weight: 700;">Total</th>
                                        <th class="text-center" style="color: #f8f9fa; font-weight: 700;">Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($ventasHoyLista as $venta)
                                    <tr>
                                        <td class="fw-bold" style="color: #ffffff; font-size: 0.95rem;">#{{ $venta->id }}</td>
                                        <td style="font-size: 0.9rem; color: #cbd5e1; font-weight: 500;">
                                            <i class="bi bi-clock me-1" style="color: var(--cy-gold);"></i>{{ $venta->created_at->format('H:i') }} hs
                                        </td>
                                        <td>
                                            @if(($venta->tipo_pago ?? 'efectivo') === 'tarjeta')
                                                <span class="badge px-2 py-1" style="background: rgba(192, 132, 252, 0.25); color: #f5d0fe; border: 1px solid rgba(192, 132, 252, 0.5); font-weight: 700; font-size: 0.82rem;">
                                                    💳 Tarjeta
                                                </span>
                                            @elseif(($venta->tipo_pago ?? 'efectivo') === 'factura')
                                                <span class="badge px-2 py-1" style="background: rgba(56, 189, 248, 0.25); color: #7dd3fc; border: 1px solid rgba(56, 189, 248, 0.5); font-weight: 700; font-size: 0.82rem;">
                                                    📄 Factura
                                                </span>
                                            @else
                                                <span class="badge px-2 py-1" style="background: rgba(74, 222, 128, 0.25); color: #4ade80; border: 1px solid rgba(74, 222, 128, 0.5); font-weight: 700; font-size: 0.82rem;">
                                                    💵 Efectivo
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-end fw-bold" style="color: var(--cy-gold); font-size: 1.05rem;">
                                            ${{ number_format($venta->total, 0, ',', '.') }}
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('ventas.show', $venta) }}" class="btn btn-sm btn-outline-light px-3 py-1 fw-semibold" style="border-color: rgba(255,255,255,0.35); color: #ffffff;" title="Ver Detalle">
                                                <i class="bi bi-eye me-1"></i>Detalle
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Vista Mobile (Tarjetas compactas en Celular) --}}
                        <div class="d-block d-md-none" style="max-height: 440px; overflow-y: auto;">
                            @foreach($ventasHoyLista as $venta)
                            <div class="sale-mobile-card">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <span class="fw-bold me-2" style="color: #ffffff; font-size: 0.98rem;">Venta #{{ $venta->id }}</span>
                                        <span style="font-size: 0.82rem; color: #cbd5e1;">
                                            <i class="bi bi-clock me-1" style="color: var(--cy-gold);"></i>{{ $venta->created_at->format('H:i') }} hs
                                        </span>
                                    </div>
                                    <div>
                                        @if(($venta->tipo_pago ?? 'efectivo') === 'tarjeta')
                                            <span class="badge px-2 py-1" style="background: rgba(192, 132, 252, 0.25); color: #f5d0fe; border: 1px solid rgba(192, 132, 252, 0.5); font-weight: 700; font-size: 0.78rem;">
                                                💳 Tarjeta
                                            </span>
                                        @elseif(($venta->tipo_pago ?? 'efectivo') === 'factura')
                                            <span class="badge px-2 py-1" style="background: rgba(56, 189, 248, 0.25); color: #7dd3fc; border: 1px solid rgba(56, 189, 248, 0.5); font-weight: 700; font-size: 0.78rem;">
                                                📄 Factura
                                            </span>
                                        @else
                                            <span class="badge px-2 py-1" style="background: rgba(74, 222, 128, 0.25); color: #4ade80; border: 1px solid rgba(74, 222, 128, 0.5); font-weight: 700; font-size: 0.78rem;">
                                                💵 Efectivo
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center pt-2" style="border-top: 1px dashed rgba(255,255,255,0.08);">
                                    <div>
                                        <span class="text-muted small">Total: </span>
                                        <span class="fw-bold" style="color: var(--cy-gold); font-size: 1.1rem;">
                                            ${{ number_format($venta->total, 0, ',', '.') }}
                                        </span>
                                    </div>
                                    <a href="{{ route('ventas.show', $venta) }}" class="btn btn-sm btn-outline-light px-3 py-1 fw-semibold" style="border-color: rgba(255,255,255,0.3); color: #ffffff; font-size: 0.82rem;">
                                        <i class="bi bi-eye me-1"></i>Ver Detalle
                                    </a>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
