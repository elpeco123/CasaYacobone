@extends('layouts.app')

@section('title', 'Caja')

@section('content')
<div class="fade-in">
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <div>
            <h1 class="text-white fw-bold mb-0">
                <i class="bi bi-safe2-fill me-2" style="color: var(--cy-gold);"></i>Caja
            </h1>
            <p class="text-muted mb-0" style="font-size: 0.9rem;">
                Cada apertura genera un período de ventas. Al cerrar, el panel de ventas se reinicia.
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge px-3 py-2" style="font-size: 0.85rem; color: #f2e6d2; background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); font-weight: 500;">
                <i class="bi bi-calendar3 me-1" style="color: var(--cy-gold);"></i>{{ now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
            </span>
        </div>
    </div>

    @if($cajaAbierta)
        {{-- ===== CAJA ABIERTA: resumen en vivo + cierre ===== --}}
        <div class="row g-4">
            <div class="col-lg-5">
                <div class="card-glass h-100" style="border: 1px solid rgba(155, 179, 95, 0.35);">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h5 class="mb-0 fw-bold text-white">
                                <i class="bi bi-unlock-fill me-2" style="color: #a9c46c;"></i>
                                Caja #{{ $cajaAbierta->id }} Abierta
                            </h5>
                            <span class="badge bg-success bg-opacity-25 text-success border border-success px-2.5 py-1" style="font-size: 0.75rem; color: #a9c46c !important; font-weight: 700;">
                                <i class="bi bi-check-circle-fill me-1"></i>En curso
                            </span>
                        </div>

                        <div class="p-3 mb-3 rounded-3" style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.1); font-size: 0.88rem; color: #cdb99c;">
                            <div class="d-flex align-items-center mb-1">
                                <i class="bi bi-person-check-fill me-2 text-warning"></i>
                                <span>Abierta por: <strong class="text-white">{{ $cajaAbierta->user->name ?? 'Usuario' }}</strong></span>
                            </div>
                            <div class="d-flex align-items-center mb-1">
                                <i class="bi bi-clock-history me-2" style="color: var(--cy-gold);"></i>
                                <span>Apertura: <strong class="text-white">{{ $cajaAbierta->fecha_apertura?->format('d/m/Y H:i') ?? '—' }}</strong></span>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-cash-stack me-2" style="color: var(--cy-gold);"></i>
                                <span>Cambio inicial: <strong class="text-white">${{ number_format($montoInicial, 0, ',', '.') }}</strong></span>
                            </div>
                            <div class="d-flex align-items-center mt-1">
                                <i class="bi bi-receipt me-2" style="color: var(--cy-gold);"></i>
                                <span>Ventas del período: <strong class="text-white">{{ $cantidadVentas }}</strong></span>
                            </div>
                        </div>

                        <p style="font-size: 0.85rem; color: #cdb99c;" class="mb-3">
                            Al cerrar se guardan la hora de cierre y los totales por medio de pago. El panel de ventas se reinicia para la próxima apertura.
                        </p>

                        <form method="POST" action="{{ route('caja.cerrar', $cajaAbierta) }}"
                              onsubmit="return confirm('¿Cerrar la caja #{{ $cajaAbierta->id }}? Se guardarán los totales del período.');">
                            @csrf
                            <button type="submit" class="btn btn-accent w-100 py-2.5 fw-bold text-uppercase d-flex align-items-center justify-content-center" style="font-size: 0.95rem; letter-spacing: 0.5px;">
                                <i class="bi bi-lock-fill me-2 fs-5"></i>
                                Cerrar Caja
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Resumen en vivo del período --}}
            <div class="col-lg-7">
                <div class="card-glass h-100">
                    <div class="card-body p-4">
                        <h5 class="mb-3 fw-bold text-white">
                            <i class="bi bi-wallet2 me-2" style="color: var(--cy-gold);"></i>
                            Resumen del Período en Vivo
                        </h5>

                        <div class="p-3 mb-4 rounded-3" style="background: linear-gradient(135deg, rgba(43, 29, 21, 0.9), rgba(60, 40, 26, 0.85)); border: 2px solid var(--cy-gold); box-shadow: 0 4px 20px rgba(0,0,0,0.3);">
                            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 mb-1">
                                <div class="w-100">
                                    <span class="badge bg-gold-light text-dark fw-bold px-2.5 py-1 mb-1" style="font-size: 0.75rem;">
                                        DINERO FÍSICO DISPONIBLE
                                    </span>
                                    <h4 class="mb-0 fw-bold text-white">Total de Efectivo en Caja</h4>
                                    <small style="color: #cdb99c;">Cambio inicial + Efectivo − Gastos</small>
                                </div>
                                <div class="text-start text-sm-end w-100">
                                    <div class="fw-extrabold text-break" style="font-size: clamp(1.4rem, 7vw, 2rem); line-height: 1.1; color: #f6c078; text-shadow: 0 0 15px rgba(246, 192, 120, 0.4);">
                                        ${{ number_format($totalEfectivoEnCaja, 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="p-3 rounded-3 mb-3" style="background: rgba(26, 17, 12, 0.6); border: 1px solid rgba(255,255,255,0.1);">
                            <div class="small fw-bold text-uppercase mb-2" style="color: #eadcc6; letter-spacing: 0.5px;">
                                Cobros del Período
                            </div>
                            <div class="d-flex justify-content-between align-items-center py-1.5 border-bottom border-secondary border-opacity-50">
                                <span style="color: #cdb99c;">Efectivo:</span>
                                <span class="fw-bold text-success">+${{ number_format($ventasEfectivo, 0, ',', '.') }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center py-1.5 border-bottom border-secondary border-opacity-50">
                                <span style="color: #cdb99c;">Gastos / retiros:</span>
                                <span class="fw-bold text-danger">−${{ number_format($totalRetiros, 0, ',', '.') }}</span>
                            </div>
                            @foreach($ventasPorForma as $tipo => $monto)
                                @continue($tipo === 'efectivo')
                                <div class="d-flex justify-content-between align-items-center py-1.5 border-bottom border-secondary border-opacity-50">
                                    <span style="color: #cdb99c;">
                                        {{ \App\Models\Venta::etiquetaPago($tipo) }}:
                                        @if($tipo === 'cuenta_corriente')
                                            <span style="color: #a8927a; font-size: 0.82rem;">(queda a cobrar)</span>
                                        @endif
                                    </span>
                                    <span class="fw-bold" style="color: {{ $tipo === 'cuenta_corriente' ? '#ecc58f' : '#cfa0c6' }};">${{ number_format($monto, 0, ',', '.') }}</span>
                                </div>
                            @endforeach
                            <div class="d-flex justify-content-between align-items-center pt-2 mt-1">
                                <span class="fw-bold text-white">Total vendido en el período:</span>
                                <span class="fw-bold fs-5" style="color: var(--cy-gold);">${{ number_format($totalVendidoCaja, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <a href="{{ route('ventas.index') }}" class="btn btn-gold w-100 py-2 fw-bold mb-4">
                            <i class="bi bi-cart-fill me-2"></i>Ver Ventas de esta Caja ({{ $cantidadVentas }})
                        </a>

                        {{-- ===== GASTOS / RETIROS DE LA CAJA ===== --}}
                        <h5 class="mb-1 fw-bold text-white">
                            <i class="bi bi-cash-coin me-2" style="color: var(--cy-accent);"></i>
                            Gastos de la Caja
                        </h5>
                        <p style="font-size: 0.85rem; color: #cdb99c;" class="mb-3">
                            Dinero gastado de esta caja (ej: yerba, limpieza). Se descuenta del efectivo físico.
                        </p>

                        <form method="POST" action="{{ route('caja.retiros.store') }}" class="row g-2 mb-3">
                            @csrf
                            <div class="col-12">
                                <select name="categoria_gasto_id" id="retiro-categoria"
                                        class="form-select form-select-dark @error('categoria_gasto_id') is-invalid @enderror" required>
                                    <option value="">Categoría del gasto *</option>
                                    @foreach($categoriasGasto as $cat)
                                        <option value="{{ $cat->id }}" {{ old('categoria_gasto_id') == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('categoria_gasto_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-5">
                                <div class="input-group">
                                    <span class="input-group-text bg-dark border-secondary fw-bold" style="color: var(--cy-gold);">$</span>
                                    <input type="number" step="any" min="1" name="monto" id="retiro-monto"
                                           class="form-control form-control-dark" placeholder="Monto *"
                                           value="{{ old('monto') }}" required>
                                </div>
                            </div>
                            <div class="col-7">
                                <input type="text" name="concepto" id="retiro-concepto"
                                       class="form-control form-control-dark" placeholder="Detalle (ej: yerba mate)"
                                       value="{{ old('concepto') }}" maxlength="255">
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-accent w-100 fw-bold">
                                    <i class="bi bi-dash-circle-fill me-1"></i>Registrar Gasto
                                </button>
                            </div>
                        </form>

                        @if($retiros->isEmpty())
                            <p class="text-center mb-0" style="font-size: 0.85rem; color: #a8927a;">
                                Sin gastos registrados en esta caja.
                            </p>
                        @else
                            <div class="table-responsive">
                                <table class="table table-dark-custom table-hover mb-0">
                                    <tbody>
                                        @foreach($retiros as $retiro)
                                            <tr>
                                                <td class="text-white">
                                                    <span class="badge me-1" style="background: rgba(192, 73, 47, 0.15); color: #ff8fa3; border: 1px solid rgba(192, 73, 47, 0.3); font-size: 0.72rem;">
                                                        {{ $retiro->categoriaGasto->nombre ?? 'Sin categoría' }}
                                                    </span>
                                                    {{ $retiro->concepto }}
                                                    <br><small class="text-muted">{{ $retiro->created_at->format('d/m H:i') }} · {{ $retiro->user->name ?? '' }}</small>
                                                </td>
                                                <td class="text-end fw-bold text-danger text-nowrap">
                                                    −${{ number_format($retiro->monto, 0, ',', '.') }}
                                                </td>
                                                <td class="text-end" style="width: 40px;">
                                                    <form method="POST" action="{{ route('caja.retiros.destroy', $retiro) }}"
                                                          onsubmit="return confirm('¿Eliminar este gasto? El monto vuelve a la caja.');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-glass btn-sm text-danger" title="Eliminar gasto">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
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
    @else
        {{-- ===== SIN CAJA ABIERTA: formulario de apertura ===== --}}
        <div class="row g-4">
            <div class="col-lg-6 mx-auto">
                <div class="card-glass h-100" style="border: 1px solid rgba(212, 154, 82, 0.35);">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h5 class="mb-0 fw-bold text-white">
                                <i class="bi bi-cash-stack me-2" style="color: var(--cy-gold);"></i>
                                Abrir Nueva Caja
                            </h5>
                            <span class="badge bg-warning bg-opacity-25 text-warning border border-warning px-2.5 py-1" style="font-size: 0.75rem; color: #fbbf24 !important; font-weight: 700;">
                                <i class="bi bi-exclamation-circle-fill me-1"></i>Sin caja abierta
                            </span>
                        </div>

                        <p style="font-size: 0.88rem; color: #cdb99c;" class="mb-4">
                            Ingresá el dinero en efectivo para dar cambio. Se inicia un nuevo período: solo verás las ventas de esta caja hasta cerrarla.
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
                                           value="{{ old('monto_inicial', '') }}"
                                           placeholder="Ej: 10000, 15000, 20000"
                                           style="font-size: 1.35rem; font-weight: 700; color: #ffffff;" required autofocus>
                                </div>
                                @error('monto_inicial')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <span class="small d-block mb-2" style="color: #cdb99c; font-weight: 500;">Montos rápidos de cambio:</span>
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
                                       value="{{ old('observaciones', '') }}"
                                       placeholder="Ej: Billetes chicos de $500 y $1.000">
                                @error('observaciones')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-gold w-100 py-2.5 fw-bold text-uppercase d-flex align-items-center justify-content-center" style="font-size: 0.95rem; letter-spacing: 0.5px;">
                                <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                                Confirmar Apertura de Caja
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if(Auth::user()->isAdmin())
        <div class="card-glass mt-4">
            <div class="card-body p-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="text-white fw-bold">
                    <i class="bi bi-clock-history me-2" style="color: var(--cy-accent);"></i>
                    Historial completo de cajas (aperturas y cierres)
                </div>
                <a href="{{ route('reportes.cajas') }}" class="btn btn-glass text-light">
                    <i class="bi bi-box-arrow-up-right me-1"></i>Ver en Reportes
                </a>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const inputMonto = document.getElementById('monto_inicial');
        if (!inputMonto) return;
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
