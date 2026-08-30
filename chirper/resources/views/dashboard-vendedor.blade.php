@extends('layouts.app')

@section('title', 'Panel de Vendedor')

@section('content')
<div class="fade-in">
    {{-- Page Header --}}
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1><i class="bi bi-person-badge-fill me-2" style="color: var(--cy-gold);"></i>Panel de Vendedor</h1>
            <p class="text-muted mb-0" style="font-size: 0.9rem;">Bienvenido/a, {{ Auth::user()->name }} · Casa Yacobone</p>
        </div>
        <div>
            <span class="text-muted" style="font-size: 0.85rem;">
                <i class="bi bi-calendar3 me-1"></i>{{ now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
            </span>
        </div>
    </div>

    {{-- Main Action Hero Card: Registrar Venta --}}
    <div class="card-glass p-4 mb-4" style="background: linear-gradient(135deg, rgba(212, 165, 116, 0.12), rgba(22, 33, 62, 0.6)); border: 1px solid rgba(212, 165, 116, 0.3);">
        <div class="row align-items-center">
            <div class="col-md-8 mb-3 mb-md-0">
                <h3 class="mb-1" style="font-weight: 800; color: #fff;">
                    <i class="bi bi-cart-plus-fill me-2" style="color: var(--cy-gold);"></i>Registrar Nueva Venta
                </h3>
                <p class="text-muted mb-0" style="font-size: 0.95rem;">
                    Accede directamente al punto de cobro para seleccionar productos, forma de pago y emitir el comprobante.
                </p>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="{{ route('ventas.create') }}" class="btn btn-gold btn-lg px-4 py-3 fw-bold w-100 w-md-auto shadow-sm" style="font-size: 1.05rem;">
                    <i class="bi bi-plus-circle-fill me-2"></i>Nueva Venta
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Resumen Cierre de Caja del Día --}}
        <div class="col-lg-5">
            <div class="card-glass h-100">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div>
                        <h5 class="mb-3" style="font-weight: 700;">
                            <i class="bi bi-wallet2 me-2" style="color: var(--cy-gold);"></i>
                            Cierre de Caja del Día
                        </h5>
                        <p class="text-muted mb-3" style="font-size: 0.82rem;">
                            Resumen acumulado por forma de pago correspondiente a la jornada de hoy.
                        </p>

                        <div class="p-3 mb-3" style="background: rgba(255,255,255,0.02); border-radius: 12px; border: 1px solid var(--cy-border);">
                            <div class="d-flex justify-content-between align-items-center py-2" style="border-bottom: 1px dashed rgba(255,255,255,0.06); font-size: 0.95rem;">
                                <span>💵 Efectivo:</span>
                                <span class="fw-bold text-success" style="font-size: 1.05rem;">${{ number_format($ventasHoyPorForma['efectivo'], 0, ',', '.') }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center py-2" style="border-bottom: 1px dashed rgba(255,255,255,0.06); font-size: 0.95rem;">
                                <span>💳 Tarjeta:</span>
                                <span class="fw-bold" style="color: #af7ac5; font-size: 1.05rem;">${{ number_format($ventasHoyPorForma['tarjeta'], 0, ',', '.') }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center py-2" style="font-size: 0.95rem;">
                                <span>📄 Factura:</span>
                                <span class="fw-bold text-info" style="font-size: 1.05rem;">${{ number_format($ventasHoyPorForma['factura'], 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <div class="p-3" style="background: rgba(212, 165, 116, 0.08); border-radius: 12px; border: 1px solid rgba(212, 165, 116, 0.25);">
                            <div class="text-muted mb-1" style="font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">
                                Total Acumulado para Cierre
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-bold text-light" style="font-size: 1.4rem; color: var(--cy-gold);">
                                        ${{ number_format($ventasHoy, 0, ',', '.') }}
                                    </div>
                                    <div class="text-muted" style="font-size: 0.8rem;">
                                        {{ $cantidadVentasHoy }} {{ $cantidadVentasHoy === 1 ? 'operación realizada' : 'operaciones realizadas' }} hoy
                                    </div>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-success bg-opacity-20 text-success border border-success px-3 py-2" style="font-size: 0.82rem;">
                                        <i class="bi bi-check-circle-fill me-1"></i>Caja Activa
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 pt-2 text-center text-muted" style="font-size: 0.78rem;">
                        <i class="bi bi-info-circle me-1"></i>Utilice este total para verificar el arqueo físico de caja al finalizar el turno.
                    </div>
                </div>
            </div>
        </div>

        {{-- Historial de Ventas del Día --}}
        <div class="col-lg-7">
            <div class="card-glass h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0" style="font-weight: 700;">
                            <i class="bi bi-clock-history me-2" style="color: var(--cy-accent);"></i>
                            Historial de Ventas del Día
                        </h5>
                        <span class="badge bg-secondary bg-opacity-20 text-light px-3 py-1" style="font-size: 0.8rem;">
                            {{ $ventasHoyLista->count() }} registradas
                        </span>
                    </div>

                    @if($ventasHoyLista->isEmpty())
                        <div class="text-center py-5">
                            <i class="bi bi-receipt text-muted" style="font-size: 3rem; opacity: 0.5;"></i>
                            <p class="text-muted mt-3 mb-1 fw-bold">Aún no hay ventas registradas hoy</p>
                            <p class="text-muted small">Presione el botón "Nueva Venta" para realizar la primera transacción.</p>
                            <a href="{{ route('ventas.create') }}" class="btn btn-gold btn-sm mt-2">
                                <i class="bi bi-plus-circle me-1"></i>Registrar Venta
                            </a>
                        </div>
                    @else
                        <div class="table-responsive" style="max-height: 420px; overflow-y: auto;">
                            <table class="table table-dark-custom table-hover align-middle mb-0">
                                <thead style="position: sticky; top: 0; background: var(--cy-primary); z-index: 10;">
                                    <tr>
                                        <th>Venta #</th>
                                        <th>Hora</th>
                                        <th>Forma de Pago</th>
                                        <th class="text-end">Total</th>
                                        <th class="text-center">Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($ventasHoyLista as $venta)
                                    <tr>
                                        <td class="fw-bold">#{{ $venta->id }}</td>
                                        <td class="text-muted" style="font-size: 0.85rem;">
                                            <i class="bi bi-clock me-1"></i>{{ $venta->created_at->format('H:i') }} hs
                                        </td>
                                        <td>
                                            @if(($venta->tipo_pago ?? 'efectivo') === 'tarjeta')
                                                <span class="badge px-2 py-1" style="background: rgba(175, 122, 197, 0.2); color: #d7bde2; border: 1px solid rgba(175, 122, 197, 0.4);">
                                                    💳 Tarjeta
                                                </span>
                                            @elseif(($venta->tipo_pago ?? 'efectivo') === 'factura')
                                                <span class="badge px-2 py-1" style="background: rgba(23, 162, 184, 0.2); color: #17a2b8; border: 1px solid rgba(23, 162, 184, 0.4);">
                                                    📄 Factura
                                                </span>
                                            @else
                                                <span class="badge px-2 py-1" style="background: rgba(40, 167, 69, 0.2); color: #28a745; border: 1px solid rgba(40, 167, 69, 0.4);">
                                                    💵 Efectivo
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-end fw-bold" style="color: var(--cy-gold);">
                                            ${{ number_format($venta->total, 0, ',', '.') }}
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('ventas.show', $venta) }}" class="btn btn-sm btn-outline-light" title="Ver Detalle">
                                                <i class="bi bi-eye"></i> Detalle
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
