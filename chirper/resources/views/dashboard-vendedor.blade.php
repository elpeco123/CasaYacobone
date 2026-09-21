@extends('layouts.app')

@section('title', 'Panel de Vendedor')

@push('styles')
<style>
    .vd { max-width: 720px; margin: 0 auto; }

    .vd-hello {
        font-family: var(--cy-font-display);
        font-weight: 700;
        font-size: 1.6rem;
        line-height: 1.15;
        color: #fbf4e8;
        margin: 0;
    }

    .vd-date {
        color: var(--cy-text-faint);
        font-size: 0.92rem;
        margin: 0.15rem 0 1.25rem;
    }

    .vd-date::first-letter { text-transform: uppercase; }

    /* ----- Tarjeta de caja: el número que importa ----- */
    .vd-caja {
        background: var(--cy-surface);
        border: 1px solid var(--cy-border);
        border-radius: 16px;
        padding: 1.25rem 1.25rem 1rem;
        position: relative;
        overflow: hidden;
    }

    .vd-caja::before {
        content: '';
        position: absolute;
        inset: 6px;
        border: 1.5px dashed rgba(212, 154, 82, 0.28);
        border-radius: 11px;
        pointer-events: none;
    }

    .vd-status {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        font-size: 0.86rem;
        font-weight: 600;
        color: #c3d690;
    }

    .vd-status::before {
        content: '';
        width: 9px;
        height: 9px;
        border-radius: 50%;
        background: var(--cy-grass);
        box-shadow: 0 0 0 3px rgba(155, 179, 95, 0.22);
    }

    .vd-status.is-closed { color: #f0c47a; }
    .vd-status.is-closed::before { background: var(--cy-warning); box-shadow: 0 0 0 3px rgba(224, 163, 58, 0.22); }

    .vd-status-time { color: var(--cy-text-faint); font-weight: 500; }

    .vd-amount-label {
        margin-top: 1rem;
        color: var(--cy-text-muted);
        font-size: 0.95rem;
    }

    .vd-amount {
        font-family: var(--cy-font-display);
        font-weight: 800;
        font-size: clamp(2.4rem, 11vw, 3.2rem);
        line-height: 1;
        letter-spacing: -1px;
        color: var(--cy-gold-light);
        font-variant-numeric: tabular-nums;
        margin: 0.2rem 0 0.35rem;
    }

    .vd-amount small {
        font-size: 0.55em;
        font-weight: 700;
        margin-right: 0.1em;
        color: var(--cy-gold);
    }

    .vd-hint { color: var(--cy-text-faint); font-size: 0.86rem; margin: 0; }

    .vd-breakdown { margin-top: 0.9rem; border-top: 1px solid var(--cy-border); }

    .vd-breakdown summary {
        list-style: none;
        cursor: pointer;
        padding: 0.75rem 0 0.2rem;
        color: var(--cy-gold);
        font-weight: 600;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }

    .vd-breakdown summary::-webkit-details-marker { display: none; }
    .vd-breakdown summary i { transition: transform 0.2s ease; }
    .vd-breakdown[open] summary i { transform: rotate(90deg); }

    .vd-line {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        padding: 0.45rem 0;
        font-size: 0.95rem;
        color: var(--cy-text-muted);
        font-variant-numeric: tabular-nums;
    }

    .vd-line strong { color: var(--cy-text); font-weight: 600; }
    .vd-line .is-plus { color: #b9cf80; }
    .vd-line .is-minus { color: #ea8469; }

    .vd-line.is-total {
        border-top: 1px dashed var(--cy-border-strong);
        margin-top: 0.3rem;
        padding-top: 0.65rem;
        color: var(--cy-text);
        font-weight: 600;
    }

    .vd-line.is-total strong { color: var(--cy-gold-light); font-family: var(--cy-font-display); font-size: 1.1rem; }

    .vd-sep { height: 0.4rem; }

    /* ----- Acciones ----- */
    .vd-sell {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.6rem;
        width: 100%;
        min-height: 64px;
        margin-top: 1rem;
        border-radius: 14px;
        background: var(--cy-gold);
        color: #22150c;
        font-family: var(--cy-font-display);
        font-weight: 700;
        font-size: 1.25rem;
        text-decoration: none;
        box-shadow: 0 3px 0 #9a6a30;
        border: 0;
    }

    .vd-sell i { font-size: 1.5rem; }
    .vd-sell:hover { background: var(--cy-gold-light); color: #22150c; }
    .vd-sell:active { transform: translateY(2px); box-shadow: 0 1px 0 #9a6a30; }

    .vd-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.6rem;
        margin-top: 0.6rem;
    }

    .vd-action {
        width: 100%;
        min-height: 52px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.45rem;
        font-weight: 600;
        font-size: 0.95rem;
        background: rgba(236, 197, 143, 0.06);
        border: 1px solid var(--cy-border-strong);
        color: var(--cy-text);
    }

    .vd-action:hover { background: rgba(236, 197, 143, 0.12); color: var(--cy-gold-light); }
    .vd-action.is-danger { color: #f0a08a; border-color: rgba(208, 85, 58, 0.45); }
    .vd-action.is-danger:hover { background: rgba(208, 85, 58, 0.12); color: #f6b8a5; }

    /* ----- Lista de ventas ----- */
    .vd-list-head {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        margin: 2rem 0 0.6rem;
    }

    .vd-list-head h2 {
        font-size: 1.2rem;
        font-weight: 700;
        margin: 0;
        color: #fbf4e8;
    }

    .vd-list-head span { color: var(--cy-text-faint); font-size: 0.9rem; }

    .vd-list {
        background: var(--cy-surface);
        border: 1px solid var(--cy-border);
        border-radius: 14px;
        overflow: hidden;
    }

    .vd-sale {
        display: grid;
        grid-template-columns: auto 1fr auto auto;
        align-items: center;
        gap: 0.85rem;
        padding: 0.9rem 1rem;
        min-height: 60px;
        text-decoration: none;
        color: var(--cy-text);
        border-top: 1px solid var(--cy-border);
    }

    .vd-sale:first-child { border-top: 0; }
    .vd-sale:hover,
    .vd-sale:active { background: rgba(212, 154, 82, 0.08); color: var(--cy-text); }

    .vd-sale-time {
        font-variant-numeric: tabular-nums;
        font-weight: 600;
        font-size: 0.95rem;
        color: var(--cy-text-muted);
        min-width: 3.1rem;
    }

    .vd-sale-id { color: var(--cy-text-faint); font-size: 0.84rem; }

    .vd-sale-total {
        font-family: var(--cy-font-display);
        font-weight: 700;
        font-size: 1.1rem;
        color: var(--cy-gold-light);
        font-variant-numeric: tabular-nums;
        text-align: right;
    }

    .vd-sale-chev { color: var(--cy-text-faint); }

    .pay-chip {
        display: inline-block;
        font-size: 0.78rem;
        font-weight: 600;
        padding: 0.2rem 0.55rem;
        border-radius: 6px;
        margin-right: 0.35rem;
    }

    .pay-efectivo { background: rgba(155, 179, 95, 0.18); color: #c3d690; }
    .pay-tarjeta { background: rgba(207, 160, 198, 0.18); color: #e2c1dc; }
    .pay-factura { background: rgba(143, 188, 212, 0.18); color: #b9d6e6; }

    .vd-empty {
        text-align: center;
        padding: 2.25rem 1.25rem;
        color: var(--cy-text-muted);
    }

    .vd-empty i { font-size: 2.2rem; color: var(--cy-text-faint); }
    .vd-empty p { margin: 0.6rem 0 0; }

    @media (min-width: 992px) {
        .vd-hello { font-size: 2rem; }
    }
</style>
@endpush

@section('content')
@php
    $pesos = fn ($monto) => number_format($monto, 0, ',', '.');
    $nombre = explode(' ', trim(Auth::user()->name))[0];
@endphp
<div class="vd fade-in">
    <h1 class="vd-hello">Hola, {{ $nombre }}</h1>
    <p class="vd-date">{{ now()->locale('es')->isoFormat('dddd D [de] MMMM') }}</p>

    @if($cajaHoy)
        {{-- ===== Caja abierta ===== --}}
        <section class="vd-caja" aria-labelledby="vd-efectivo">
            <div class="vd-status">
                Caja abierta
                @if($cajaHoy->fecha_apertura)
                    <span class="vd-status-time">desde las {{ $cajaHoy->fecha_apertura->format('H:i') }}</span>
                @endif
            </div>

            <div class="vd-amount-label" id="vd-efectivo">Efectivo en caja</div>
            <div class="vd-amount"><small>$</small>{{ $pesos($totalEfectivoEnCaja) }}</div>
            <p class="vd-hint">Es la plata que tiene que haber en el cajón al cerrar.</p>

            <details class="vd-breakdown">
                <summary><i class="bi bi-chevron-right"></i>Ver el detalle</summary>
                <div class="vd-line"><span>Cambio inicial</span><strong>${{ $pesos($montoInicialCaja) }}</strong></div>
                <div class="vd-line"><span>Ventas en efectivo</span><strong class="is-plus">+${{ $pesos($ventasHoyPorForma['efectivo']) }}</strong></div>
                <div class="vd-line"><span>Gastos</span><strong class="is-minus">−${{ $pesos($totalRetirosCaja ?? 0) }}</strong></div>
                <div class="vd-sep"></div>
                <div class="vd-line"><span>Tarjeta</span><strong>${{ $pesos($ventasHoyPorForma['tarjeta']) }}</strong></div>
                <div class="vd-line"><span>Factura</span><strong>${{ $pesos($ventasHoyPorForma['factura']) }}</strong></div>
                <div class="vd-line is-total"><span>Total de la caja</span><strong>${{ $pesos($totalCierreGeneral) }}</strong></div>
            </details>
        </section>

        <a href="{{ route('ventas.create') }}" class="vd-sell">
            <i class="bi bi-plus-circle-fill"></i>Nueva venta
        </a>

        <div class="vd-actions">
            <button type="button" class="vd-action" data-bs-toggle="modal" data-bs-target="#modalGasto">
                <i class="bi bi-dash-circle"></i>Anotar gasto
            </button>
            <form method="POST" action="{{ route('caja.cerrar', $cajaHoy) }}"
                  onsubmit="return confirm('¿Cerrar la caja? Contá el efectivo antes: tendría que haber ${{ $pesos($totalEfectivoEnCaja) }}.');">
                @csrf
                <button type="submit" class="vd-action is-danger">
                    <i class="bi bi-lock"></i>Cerrar caja
                </button>
            </form>
        </div>

        {{-- ===== Ventas de esta caja ===== --}}
        <div class="vd-list-head">
            <h2>Ventas de esta caja</h2>
            <span>{{ $cantidadVentasHoy }} {{ $cantidadVentasHoy === 1 ? 'venta' : 'ventas' }} por ${{ $pesos($ventasHoy) }}</span>
        </div>

        <div class="vd-list">
            @forelse($ventasHoyLista as $venta)
                @php $pago = $venta->tipo_pago ?? 'efectivo'; @endphp
                <a href="{{ route('ventas.show', $venta) }}" class="vd-sale">
                    <span class="vd-sale-time">{{ $venta->created_at->format('H:i') }}</span>
                    <span>
                        <span class="pay-chip pay-{{ $pago }}">{{ ucfirst($pago) }}</span>
                        <span class="vd-sale-id">N.º {{ $venta->id }}</span>
                    </span>
                    <span class="vd-sale-total">${{ $pesos($venta->total) }}</span>
                    <i class="bi bi-chevron-right vd-sale-chev" aria-hidden="true"></i>
                </a>
            @empty
                <div class="vd-empty">
                    <i class="bi bi-receipt"></i>
                    <p>Todavía no hay ventas en esta caja. Tocá <strong>Nueva venta</strong> para cargar la primera.</p>
                </div>
            @endforelse
        </div>

        {{-- Modal: anotar gasto de la caja abierta --}}
        <div class="modal fade" id="modalGasto" tabindex="-1" aria-labelledby="modalGastoTitulo" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="border-radius: 16px;">
                    <form method="POST" action="{{ route('caja.retiros.store') }}">
                        @csrf
                        <div class="modal-header" style="border-bottom: 1px solid var(--cy-border);">
                            <h5 class="modal-title fw-bold" id="modalGastoTitulo">Anotar gasto</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            <p class="small mb-3" style="color: var(--cy-text-faint);">La plata que sacás del cajón se descuenta del efectivo en caja.</p>
                            <div class="mb-3">
                                <label for="gasto-monto" class="form-label">Monto</label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text fw-bold">$</span>
                                    <input type="number" inputmode="numeric" step="any" min="1" name="monto" id="gasto-monto"
                                           class="form-control form-control-dark" placeholder="5000" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="gasto-categoria" class="form-label">En qué se gastó</label>
                                <select name="categoria_gasto_id" id="gasto-categoria" class="form-select form-select-dark" required>
                                    <option value="">Elegí una categoría</option>
                                    @foreach($categoriasGasto as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-0">
                                <label for="gasto-concepto" class="form-label">Detalle (opcional)</label>
                                <input type="text" name="concepto" id="gasto-concepto"
                                       class="form-control form-control-dark" placeholder="Ej: yerba y azúcar" maxlength="255">
                            </div>
                        </div>
                        <div class="modal-footer" style="border-top: 1px solid var(--cy-border);">
                            <button type="button" class="btn btn-glass" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-accent">Guardar gasto</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @else
        {{-- ===== Caja cerrada ===== --}}
        <section class="vd-caja">
            <div class="vd-status is-closed">Caja cerrada</div>
            <div class="vd-amount-label">Para empezar a vender, abrí la caja con el cambio que hay en el cajón.</div>
            <a href="{{ route('caja.index') }}" class="vd-sell">
                <i class="bi bi-unlock-fill"></i>Abrir caja
            </a>
        </section>
    @endif
</div>
@endsection
