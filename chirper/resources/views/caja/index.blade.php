@extends('layouts.app')

@section('title', 'Apertura de Caja & Cambio Inicial')

@section('content')
<div class="fade-in">
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <div>
            <h1 class="text-white fw-bold mb-0">
                <i class="bi bi-safe2-fill me-2" style="color: var(--cy-gold);"></i>Apertura de Caja & Cambio Inicial
            </h1>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge px-3 py-2" style="font-size: 0.85rem; color: #f8f9fa; background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); font-weight: 500;">
                <i class="bi bi-calendar3 me-1" style="color: var(--cy-gold);"></i>{{ now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
            </span>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-custom-success alert-dismissible fade show mb-4 p-3 d-flex align-items-center" role="alert">
            <i class="bi bi-check-circle-fill fs-4 me-3"></i>
            <div class="fw-semibold">{{ session('success') }}</div>
            <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        {{-- Formulario para Abrir / Modificar Cambio en Caja --}}
        <div class="col-lg-5">
            <div class="card-glass h-100" style="border: 1px solid rgba(212, 165, 116, 0.35);">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="mb-0 fw-bold text-white">
                            <i class="bi bi-cash-stack me-2" style="color: var(--cy-gold);"></i>
                            {{ $cajaHoy ? 'Actualizar Cambio en Caja' : 'Abrir Caja del Día' }}
                        </h5>
                        @if($cajaHoy)
                            <span class="badge bg-success bg-opacity-25 text-success border border-success px-2.5 py-1" style="font-size: 0.75rem; color: #4ade80 !important; font-weight: 700;">
                                <i class="bi bi-check-circle-fill me-1"></i>Caja Abierta
                            </span>
                        @else
                            <span class="badge bg-warning bg-opacity-25 text-warning border border-warning px-2.5 py-1" style="font-size: 0.75rem; color: #fbbf24 !important; font-weight: 700;">
                                <i class="bi bi-exclamation-circle-fill me-1"></i>Pendiente de Apertura
                            </span>
                        @endif
                    </div>

                    <p style="font-size: 0.88rem; color: #cbd5e1;" class="mb-4">
                        Ingresá el dinero en efectivo con el que se inicia la jornada para dar cambio. Este monto se suma automáticamente al efectivo de las ventas para el arqueo final.
                    </p>

                    <form method="POST" action="{{ route('caja.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="monto_inicial" class="form-label fw-semibold text-white d-flex justify-content-between">
                                <span>Cambio Inicial en Efectivo *</span>
                                <span class="text-gold-light small">Pesos Argentinos ($)</span>
                            </label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-dark border-secondary fw-bold" style="color: var(--cy-gold); font-size: 1.3rem;">$</span>
                                <input type="number" step="any" min="0" name="monto_inicial" id="monto_inicial"
                                       class="form-control form-control-dark @error('monto_inicial') is-invalid @enderror"
                                       value="{{ old('monto_inicial', $cajaHoy ? $cajaHoy->monto_inicial : '') }}"
                                       placeholder="Ej: 10000, 15000, 20000"
                                       style="font-size: 1.35rem; font-weight: 700; color: #ffffff;" required autofocus>
                            </div>
                            @error('monto_inicial')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Botones de acceso rápido para montos comunes --}}
                        <div class="mb-3">
                            <span class="small d-block mb-2" style="color: #cbd5e1; font-weight: 500;">Montos rápidos de cambio:</span>
                            <div class="d-flex flex-wrap gap-2">
                                <button type="button" class="btn btn-glass btn-sm text-light fw-bold btn-preset-monto" data-monto="5000">+$5.000</button>
                                <button type="button" class="btn btn-glass btn-sm text-light fw-bold btn-preset-monto" data-monto="10000">+$10.000</button>
                                <button type="button" class="btn btn-glass btn-sm text-light fw-bold btn-preset-monto" data-monto="15000">+$15.000</button>
                                <button type="button" class="btn btn-glass btn-sm text-light fw-bold btn-preset-monto" data-monto="20000">+$20.000</button>
                                <button type="button" class="btn btn-glass btn-sm text-light fw-bold btn-preset-monto" data-monto="50000">+$50.000</button>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="observaciones" class="form-label fw-semibold text-white">Observaciones / Notas (Opcional)</label>
                            <input type="text" name="observaciones" id="observaciones"
                                   class="form-control form-control-dark @error('observaciones') is-invalid @enderror"
                                   value="{{ old('observaciones', $cajaHoy ? $cajaHoy->observaciones : '') }}"
                                   placeholder="Ej: Billetes chicos de $500 y $1.000">
                            @error('observaciones')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        @if($cajaHoy)
                            <div class="p-3 mb-3 rounded-3" style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.1); font-size: 0.82rem; color: #cbd5e1;">
                                <div class="d-flex align-items-center mb-1">
                                    <i class="bi bi-person-check-fill me-2 text-warning"></i>
                                    <span>Última apertura por: <strong class="text-white">{{ $cajaHoy->user->name ?? 'Usuario' }}</strong></span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-clock-history me-2" style="color: var(--cy-gold);"></i>
                                    <span>Hora de registro: <strong class="text-white">{{ $cajaHoy->updated_at->format('H:i:s') }}</strong></span>
                                </div>
                            </div>
                        @endif

                        <button type="submit" class="btn btn-gold w-100 py-2.5 fw-bold text-uppercase d-flex align-items-center justify-content-center" style="font-size: 0.95rem; letter-spacing: 0.5px;">
                            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                            {{ $cajaHoy ? 'Actualizar Cambio en Caja' : 'Confirmar Apertura de Caja' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Panel de Estado Actual del Efectivo y Totales --}}
        <div class="col-lg-7">
            <div class="card-glass h-100">
                <div class="card-body p-4">
                    <h5 class="mb-3 fw-bold text-white">
                        <i class="bi bi-wallet2 me-2" style="color: var(--cy-gold);"></i>
                        Resumen de Efectivo & Cierre en Vivo
                    </h5>
                    <p style="font-size: 0.88rem; color: #cbd5e1;" class="mb-4">
                        Cálculo en tiempo real del dinero físico disponible en caja más los cobros digitales del día.
                    </p>

                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <div class="p-3 rounded-3" style="background: rgba(212, 165, 116, 0.12); border: 1px solid rgba(212, 165, 116, 0.3);">
                                <div class="small fw-semibold mb-1" style="color: #cbd5e1;">🪙 Cambio Inicial (Apertura)</div>
                                <div class="fs-4 fw-bold" style="color: var(--cy-gold);">${{ number_format($montoInicial, 0, ',', '.') }}</div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 rounded-3" style="background: rgba(74, 222, 128, 0.12); border: 1px solid rgba(74, 222, 128, 0.3);">
                                <div class="small fw-semibold mb-1" style="color: #cbd5e1;">💵 Ventas en Efectivo Hoy</div>
                                <div class="fs-4 fw-bold" style="color: #4ade80;">+${{ number_format($ventasHoyEfectivo, 0, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Caja FÍSICA TOTAL --}}
                    <div class="p-3 mb-4 rounded-3" style="background: linear-gradient(135deg, rgba(22, 33, 62, 0.9), rgba(15, 52, 96, 0.85)); border: 2px solid var(--cy-gold); box-shadow: 0 4px 20px rgba(0,0,0,0.3);">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <div>
                                <span class="badge bg-gold-light text-dark fw-bold px-2.5 py-1 mb-1" style="font-size: 0.75rem;">
                                    DINERO FÍSICO DISPONIBLE
                                </span>
                                <h4 class="mb-0 fw-bold text-white">Total de Efectivo en Caja</h4>
                                <small style="color: #cbd5e1;">Cambio inicial (${{ number_format($montoInicial, 0, ',', '.') }}) + Ventas en efectivo (${{ number_format($ventasHoyEfectivo, 0, ',', '.') }})</small>
                            </div>
                            <div class="text-end">
                                <div class="fw-extrabold" style="font-size: 2rem; color: #f6c078; text-shadow: 0 0 15px rgba(246, 192, 120, 0.4);">
                                    ${{ number_format($totalEfectivoEnCaja, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Desglose Completo de Cobros del Día --}}
                    <div class="p-3 rounded-3 mb-3" style="background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(255,255,255,0.1);">
                        <div class="small fw-bold text-uppercase mb-2" style="color: #e2e8f0; letter-spacing: 0.5px;">
                            Otras Formas de Cobro Hoy
                        </div>
                        <div class="d-flex justify-content-between align-items-center py-1.5 border-bottom border-secondary border-opacity-50">
                            <span style="color: #cbd5e1;">💳 Tarjeta:</span>
                            <span class="fw-bold" style="color: #c084fc;">${{ number_format($ventasHoyTarjeta, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center py-1.5 border-bottom border-secondary border-opacity-50">
                            <span style="color: #cbd5e1;">📄 Factura:</span>
                            <span class="fw-bold" style="color: #38bdf8;">${{ number_format($ventasHoyFactura, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center pt-2 mt-1">
                            <span class="fw-bold text-white">Total General Vendido Hoy:</span>
                            <span class="fw-bold fs-5" style="color: var(--cy-gold);">${{ number_format($totalVendidoHoy, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Historial de Cajas Anteriores --}}
    <div class="card-glass mt-4">
        <div class="card-body p-4">
            <h5 class="mb-3 fw-bold text-white">
                <i class="bi bi-clock-history me-2" style="color: var(--cy-accent);"></i>
                Historial de Aperturas de Caja
            </h5>

            <div class="table-responsive">
                <table class="table table-dark-custom table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Responsable</th>
                            <th class="text-end">Cambio Inicial</th>
                            <th>Observaciones</th>
                            <th class="text-end">Hora de Registro</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($historialCajas as $item)
                        <tr>
                            <td class="fw-bold text-white">
                                <i class="bi bi-calendar-event me-1" style="color: var(--cy-gold);"></i>
                                {{ $item->fecha->format('d/m/Y') }}
                                @if($item->fecha->isToday())
                                    <span class="badge bg-success bg-opacity-25 text-success border border-success ms-1" style="font-size: 0.7rem; color: #4ade80 !important;">Hoy</span>
                                @endif
                            </td>
                            <td class="text-light">
                                <i class="bi bi-person-circle me-1" style="color: #cbd5e1;"></i>
                                {{ $item->user->name ?? 'Usuario' }}
                            </td>
                            <td class="text-end fw-bold" style="color: var(--cy-gold); font-size: 1rem;">
                                ${{ number_format($item->monto_inicial, 0, ',', '.') }}
                            </td>
                            <td style="color: #cbd5e1; font-size: 0.88rem;">
                                {{ $item->observaciones ?: '—' }}
                            </td>
                            <td class="text-end" style="color: #94a3b8; font-size: 0.85rem;">
                                {{ $item->updated_at->format('H:i:s') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4" style="color: #cbd5e1;">
                                <i class="bi bi-inbox fs-2 mb-2 d-block text-muted"></i>
                                No hay registros de apertura de caja anteriores.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const inputMonto = document.getElementById('monto_inicial');
        document.querySelectorAll('.btn-preset-monto').forEach(btn => {
            btn.addEventListener('click', function() {
                const addMonto = parseFloat(this.dataset.monto) || 0;
                let currentVal = parseFloat(inputMonto.value) || 0;
                inputMonto.value = currentVal + addMonto;
                inputMonto.focus();
            });
        });
    });
</script>
@endpush
@endsection
