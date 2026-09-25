@extends('layouts.app')

@section('title', $cliente->nombreCompleto())

@section('content')
<div class="fade-in">
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <nav class="breadcrumb mb-1">
                <span class="breadcrumb-item"><a href="{{ route('cuentas-corrientes.index') }}">Cuentas corrientes</a></span>
                <span class="breadcrumb-item active">{{ $cliente->nombreCompleto() }}</span>
            </nav>
            <h1 class="mb-0">{{ $cliente->nombreCompleto() }}</h1>
            <p class="text-muted mb-0" style="font-size: 0.9rem;">
                {{ $cliente->telefono }} · {{ $cliente->direccion }}
            </p>
        </div>
    </div>

    <div class="row g-4">
        {{-- Saldo y cobro --}}
        <div class="col-lg-5">
            <div class="card-glass mb-4">
                <div class="card-body">
                    <div class="text-muted" style="font-size: 0.9rem;">Debe</div>
                    <div style="font-family: var(--cy-font-display); font-weight: 800; font-size: 2.6rem; line-height: 1.1; color: var(--cy-gold-light); font-variant-numeric: tabular-nums;">
                        ${{ number_format($saldo, 0, ',', '.') }}
                    </div>
                    @if($desde)
                        <p class="mb-0" style="font-size: 0.88rem; color: var(--cy-text-faint);">
                            La deuda más vieja es del {{ $desde->format('d/m/Y') }}, hace {{ (int) $desde->diffInDays(now(), absolute: true) }} días.
                        </p>
                    @else
                        <p class="mb-0" style="font-size: 0.88rem; color: #c3d690;">Sin deuda pendiente.</p>
                    @endif
                </div>
            </div>

            @if($saldo > 0)
                <div class="card-glass mb-4">
                    <div class="card-body">
                        <h5 class="mb-3" style="font-weight: 700;">Registrar cobro</h5>
                        <form method="POST" action="{{ route('cuentas-corrientes.pagar', $cliente) }}">
                            @csrf
                            <div class="mb-3">
                                <label for="monto" class="form-label">Cuánto paga</label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text fw-bold">$</span>
                                    <input type="number" step="any" min="1" max="{{ $saldo }}" name="monto" id="monto"
                                           class="form-control form-control-dark" value="{{ old('monto') }}"
                                           placeholder="{{ number_format($saldo, 0, '', '') }}" required>
                                </div>
                                <button type="button" class="btn btn-glass btn-sm mt-2" id="btnSaldarTodo">
                                    Saldar todo (${{ number_format($saldo, 0, ',', '.') }})
                                </button>
                            </div>
                            <div class="mb-3">
                                <label for="tipo_pago" class="form-label">Con qué paga</label>
                                <select name="tipo_pago" id="tipo_pago" class="form-select form-select-dark" required>
                                    @foreach(\App\Models\Venta::PAGOS as $valor => $etiqueta)
                                        @continue($valor === 'cuenta_corriente')
                                        <option value="{{ $valor }}" {{ old('tipo_pago') === $valor ? 'selected' : '' }}>{{ $etiqueta }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="nota" class="form-label">Nota (opcional)</label>
                                <input type="text" name="nota" id="nota" maxlength="160"
                                       class="form-control form-control-dark" value="{{ old('nota') }}"
                                       placeholder="Ej: entregó a cuenta">
                            </div>
                            <button type="submit" class="btn btn-gold w-100 py-2 fw-bold">Guardar cobro</button>
                        </form>
                    </div>
                </div>
            @endif

            <div class="card-glass">
                <div class="card-body">
                    <h5 class="mb-3" style="font-weight: 700;">Cobros</h5>
                    @forelse($pagos as $pago)
                        <div class="d-flex justify-content-between align-items-start py-2" style="border-top: 1px solid var(--cy-border);">
                            <div>
                                <div class="fw-bold">{{ $pago->created_at->format('d/m/Y') }}</div>
                                <div style="font-size: 0.82rem; color: var(--cy-text-faint);">
                                    @include('partials.chip-pago', ['tipo' => $pago->tipo_pago])
                                    {{ $pago->nota }}
                                </div>
                            </div>
                            <span class="fw-bold" style="color: #a9c46c;">−${{ number_format($pago->monto, 0, ',', '.') }}</span>
                        </div>
                    @empty
                        <p class="text-muted mb-0" style="font-size: 0.9rem;">Todavía no pagó nada.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Compras a cuenta corriente --}}
        <div class="col-lg-7">
            <div class="card-glass">
                <div class="card-body">
                    <h5 class="mb-3" style="font-weight: 700;">Compras a cuenta corriente</h5>
                    <div class="table-responsive">
                        <table class="table table-dark-custom table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Venta</th>
                                    <th>Vendedor</th>
                                    <th class="text-end">Importe</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ventas as $venta)
                                    <tr>
                                        <td style="color: #cdb99c;">{{ $venta->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('ventas.show', $venta) }}" class="d-inline-block py-1">N.º {{ $venta->id }}</a>
                                        </td>
                                        <td style="color: #cdb99c;">{{ $venta->user->name ?? '—' }}</td>
                                        <td class="text-end fw-bold" style="color: var(--cy-gold);">
                                            ${{ number_format($venta->total, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center text-muted py-4">Sin compras registradas.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('btnSaldarTodo')?.addEventListener('click', function () {
        const monto = document.getElementById('monto');
        monto.value = {{ $saldo }};
        monto.focus();
    });
</script>
@endpush
