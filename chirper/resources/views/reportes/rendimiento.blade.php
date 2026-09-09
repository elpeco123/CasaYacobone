@extends('layouts.app')

@section('title', 'Rendimiento')

@php
    $meses = [1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'];
    $periodoLabel = $tipo === 'mensual' ? "{$meses[$mes]} de {$anio}" : "Año {$anio}";
    $esMensual = $tipo === 'mensual';
@endphp

@section('content')
<div class="fade-in">
    {{-- Encabezado --}}
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h1><i class="bi bi-bar-chart-line-fill me-2" style="color: var(--cy-gold);"></i>Rendimiento</h1>
            <p class="text-muted mb-0" style="font-size: 0.9rem;">Análisis de ventas · {{ $periodoLabel }}</p>
        </div>
    </div>

    {{-- 0. Selector de período --}}
    <div class="card-glass mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('reportes.rendimiento') }}" class="row g-2 align-items-end" id="filtro-rendimiento">
                <div class="col-6 col-md-3">
                    <label class="form-label" for="f-tipo">Período</label>
                    <select name="tipo" id="f-tipo" class="form-select form-select-dark">
                        <option value="mensual" {{ $tipo === 'mensual' ? 'selected' : '' }}>Mensual</option>
                        <option value="anual" {{ $tipo === 'anual' ? 'selected' : '' }}>Anual</option>
                    </select>
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label" for="f-anio">Año</label>
                    <select name="anio" id="f-anio" class="form-select form-select-dark">
                        @foreach($aniosDisponibles as $a)
                            <option value="{{ $a }}" {{ $anio == $a ? 'selected' : '' }}>{{ $a }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-3" id="wrap-mes" style="{{ $esMensual ? '' : 'display:none;' }}">
                    <label class="form-label" for="f-mes">Mes</label>
                    <select name="mes" id="f-mes" class="form-select form-select-dark">
                        @foreach($meses as $num => $nombre)
                            <option value="{{ $num }}" {{ $mes == $num ? 'selected' : '' }}>{{ $nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-3">
                    <button type="submit" class="btn btn-gold w-100">
                        <i class="bi bi-funnel-fill me-1"></i>Aplicar
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if(!$hayDatos)
        <div class="alert alert-custom-warning" role="alert">
            <i class="bi bi-inbox me-2"></i>No hay ventas registradas en {{ $periodoLabel }}. Probá con otro período.
        </div>
    @else
        {{-- Resumen del período --}}
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="kpi-card kpi-green">
                    <div class="kpi-value">${{ number_format($totalPeriodo, 0, ',', '.') }}</div>
                    <div class="kpi-label">Total vendido</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="kpi-card kpi-blue">
                    <div class="kpi-value">{{ number_format($cantidadPeriodo, 0, ',', '.') }}</div>
                    <div class="kpi-label">Ventas</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="kpi-card kpi-gold">
                    <div class="kpi-value">${{ number_format($ticketPeriodo, 0, ',', '.') }}</div>
                    <div class="kpi-label">Ticket promedio</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="kpi-card kpi-red">
                    <div class="kpi-value">@if($conCostos)${{ number_format($gananciaPeriodo, 0, ',', '.') }}@else — @endif</div>
                    <div class="kpi-label">Ganancia estimada</div>
                </div>
            </div>
        </div>

        @if($comparacionMes)
            <div class="alert alert-custom-success" role="alert">
                <i class="bi bi-graph-up-arrow me-2"></i>
                vs. {{ $meses[$mes] }} de {{ $anio - 1 }} (${{ number_format($comparacionMes['total'], 0, ',', '.') }}):
                <strong class="{{ $comparacionMes['variacion'] >= 0 ? '' : 'text-danger' }}">
                    {{ $comparacionMes['variacion'] >= 0 ? '+' : '' }}{{ number_format($comparacionMes['variacion'], 1, ',', '.') }}%
                </strong>
            </div>
        @endif

        {{-- 1. Evolución de ventas (gráfico principal) --}}
        <div class="card-glass mb-4">
            <div class="card-body">
                <h5 class="mb-1" style="font-weight: 700;">
                    <i class="bi bi-graph-up me-2" style="color: var(--cy-gold);"></i>Evolución de ventas
                </h5>
                <p class="text-muted mb-3" style="font-size: 0.82rem;">
                    {{ $esMensual ? 'Total vendido por día del mes' : 'Total vendido por mes' }}
                </p>
                <div class="chart-loading text-center py-4" data-chart="ch-evolucion">
                    <div class="spinner-border" style="color: var(--cy-gold);" role="status"></div>
                </div>
                <div style="position: relative; height: 300px;">
                    <canvas id="ch-evolucion"></canvas>
                </div>
            </div>
        </div>

        {{-- 2 + 3. Cantidad de ventas y ticket promedio --}}
        <div class="row g-4 mb-4">
            <div class="col-lg-6">
                <div class="card-glass h-100">
                    <div class="card-body">
                        <h5 class="mb-1" style="font-weight: 700;">
                            <i class="bi bi-bar-chart-fill me-2" style="color: #3498db;"></i>Cantidad de ventas
                        </h5>
                        <p class="text-muted mb-3" style="font-size: 0.82rem;">
                            {{ $esMensual ? 'Operaciones por día' : 'Operaciones por mes' }}
                        </p>
                        <div class="chart-loading text-center py-4" data-chart="ch-cantidad">
                            <div class="spinner-border text-info" role="status"></div>
                        </div>
                        <div style="position: relative; height: 260px;">
                            <canvas id="ch-cantidad"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card-glass h-100">
                    <div class="card-body">
                        <h5 class="mb-1" style="font-weight: 700;">
                            <i class="bi bi-receipt me-2" style="color: #2ecc71;"></i>Ticket promedio
                        </h5>
                        <p class="text-muted mb-3" style="font-size: 0.82rem;">Total vendido / cantidad de ventas</p>
                        <div class="chart-loading text-center py-4" data-chart="ch-ticket">
                            <div class="spinner-border text-success" role="status"></div>
                        </div>
                        <div style="position: relative; height: 260px;">
                            <canvas id="ch-ticket"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 4. Ventas por categoría --}}
        <div class="card-glass mb-4">
            <div class="card-body">
                <h5 class="mb-1" style="font-weight: 700;">
                    <i class="bi bi-pie-chart-fill me-2" style="color: #af7ac5;"></i>Ventas por categoría
                </h5>
                <p class="text-muted mb-3" style="font-size: 0.82rem;">Distribución del importe vendido</p>
                <div class="row g-3 align-items-center">
                    <div class="col-lg-5">
                        <div class="chart-loading text-center py-4" data-chart="ch-categorias">
                            <div class="spinner-border" style="color: #af7ac5;" role="status"></div>
                        </div>
                        <div style="position: relative; height: 260px;">
                            <canvas id="ch-categorias"></canvas>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="table-responsive">
                            <table class="table table-dark-custom table-hover mb-0">
                                <thead>
                                    <tr><th>Categoría</th><th class="text-end">Importe</th><th class="text-end">%</th></tr>
                                </thead>
                                <tbody>
                                    @foreach($categorias['nombres'] as $i => $nombre)
                                        <tr>
                                            <td class="text-white">{{ $nombre }}</td>
                                            <td class="text-end fw-bold" style="color: var(--cy-gold);">
                                                ${{ number_format($categorias['totales'][$i], 0, ',', '.') }}
                                            </td>
                                            <td class="text-end">{{ number_format($categorias['porcentajes'][$i], 1, ',', '.') }}%</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 5. Top 10 productos más vendidos --}}
        <div class="card-glass mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <h5 class="mb-0" style="font-weight: 700;">
                        <i class="bi bi-trophy-fill me-2" style="color: var(--cy-gold);"></i>Productos más vendidos · Top 10
                    </h5>
                    <div class="btn-group btn-group-sm" role="group" aria-label="Ordenar top">
                        <a href="{{ request()->fullUrlWithQuery(['orden' => 'unidades']) }}"
                           class="btn {{ $ordenTop === 'unidades' ? 'btn-gold' : 'btn-glass text-light' }}">Por unidades</a>
                        <a href="{{ request()->fullUrlWithQuery(['orden' => 'total']) }}"
                           class="btn {{ $ordenTop === 'total' ? 'btn-gold' : 'btn-glass text-light' }}">Por importe</a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-dark-custom table-hover mb-0">
                        <thead>
                            <tr><th>#</th><th>Producto</th><th>Categoría</th><th class="text-center">Unidades</th><th class="text-end">Total vendido</th></tr>
                        </thead>
                        <tbody>
                            @forelse($topUnidades as $i => $p)
                                <tr>
                                    <td class="text-muted">{{ $i + 1 }}</td>
                                    <td class="text-white fw-bold">{{ $p['producto'] }}</td>
                                    <td style="color: #cbd5e1;">{{ $p['categoria'] }}</td>
                                    <td class="text-center">{{ number_format($p['unidades'], 0, ',', '.') }}</td>
                                    <td class="text-end fw-bold" style="color: var(--cy-gold);">
                                        ${{ number_format($p['total'], 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted py-3">Sin datos.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- 6. Productos que más facturan --}}
        <div class="card-glass mb-4">
            <div class="card-body">
                <h5 class="mb-1" style="font-weight: 700;">
                    <i class="bi bi-cash-stack me-2" style="color: #2ecc71;"></i>Productos que más facturan · Top 10
                </h5>
                <p class="text-muted mb-3" style="font-size: 0.82rem;">Ranking por importe total (independiente del ranking por unidades)</p>
                <div class="chart-loading text-center py-4" data-chart="ch-facturacion">
                    <div class="spinner-border text-success" role="status"></div>
                </div>
                <div style="position: relative; height: 320px;">
                    <canvas id="ch-facturacion"></canvas>
                </div>
            </div>
        </div>

        {{-- 7. Rentabilidad (solo si hay costos confiables) --}}
        @if($conCostos)
            <div class="card-glass mb-4" style="border-color: rgba(46, 204, 113, 0.25);">
                <div class="card-body">
                    <h5 class="mb-1" style="font-weight: 700;">
                        <i class="bi bi-piggy-bank-fill me-2" style="color: #2ecc71;"></i>Ganancia
                    </h5>
                    <p class="text-muted mb-3" style="font-size: 0.82rem;">
                        Ventas − costo de los productos vendidos · {{ $esMensual ? 'por día' : 'por mes' }}
                    </p>
                    <div class="chart-loading text-center py-4" data-chart="ch-ganancia">
                        <div class="spinner-border text-success" role="status"></div>
                    </div>
                    <div style="position: relative; height: 260px;" class="mb-4">
                        <canvas id="ch-ganancia"></canvas>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-dark-custom table-hover mb-0">
                            <thead>
                                <tr><th>Producto</th><th class="text-end">Ventas</th><th class="text-end">Costo</th><th class="text-end">Ganancia</th><th class="text-end">Margen %</th></tr>
                            </thead>
                            <tbody>
                                @foreach($rentabilidad as $r)
                                    <tr>
                                        <td class="text-white">{{ $r['producto'] }}</td>
                                        <td class="text-end">${{ number_format($r['ventas'], 0, ',', '.') }}</td>
                                        <td class="text-end" style="color: #cbd5e1;">${{ number_format($r['costo'], 0, ',', '.') }}</td>
                                        <td class="text-end fw-bold {{ $r['ganancia'] >= 0 ? 'text-success' : 'text-danger' }}">
                                            ${{ number_format($r['ganancia'], 0, ',', '.') }}
                                        </td>
                                        <td class="text-end">{{ number_format($r['margen'], 1, ',', '.') }}%</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @else
            <div class="alert alert-custom-warning mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                Sección de ganancia no disponible: solo el {{ number_format($coberturaCosto, 1, ',', '.') }}% de lo vendido
                tiene costo registrado. No se calculan ganancias sin información confiable.
            </div>
        @endif

        {{-- 8. Medios de pago --}}
        <div class="card-glass mb-4">
            <div class="card-body">
                <h5 class="mb-1" style="font-weight: 700;">
                    <i class="bi bi-wallet2 me-2" style="color: var(--cy-gold);"></i>Medios de pago
                </h5>
                <p class="text-muted mb-3" style="font-size: 0.82rem;">Importe, cantidad y participación</p>
                <div class="row g-3 align-items-center">
                    <div class="col-lg-5">
                        <div class="chart-loading text-center py-4" data-chart="ch-pagos">
                            <div class="spinner-border" style="color: var(--cy-gold);" role="status"></div>
                        </div>
                        <div style="position: relative; height: 240px;">
                            <canvas id="ch-pagos"></canvas>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="table-responsive">
                            <table class="table table-dark-custom table-hover mb-0">
                                <thead>
                                    <tr><th>Medio</th><th class="text-end">Importe</th><th class="text-center">Ventas</th><th class="text-end">%</th></tr>
                                </thead>
                                <tbody>
                                    @foreach($pagos as $medio => $d)
                                        <tr>
                                            <td class="text-white text-capitalize">{{ $medio }}</td>
                                            <td class="text-end fw-bold" style="color: var(--cy-gold);">
                                                ${{ number_format($d['total'], 0, ',', '.') }}
                                            </td>
                                            <td class="text-center">{{ number_format($d['cantidad'], 0, ',', '.') }}</td>
                                            <td class="text-end">{{ number_format($d['total'] / $totalPagos * 100, 1, ',', '.') }}%</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 9. Tabla resumen + 10. comparación anual --}}
        <div class="card-glass mb-4">
            <div class="card-body">
                <h5 class="mb-3" style="font-weight: 700;">
                    <i class="bi bi-table me-2" style="color: var(--cy-gold);"></i>
                    Resumen {{ $esMensual ? 'diario' : 'mensual' }}
                </h5>
                <div class="table-responsive">
                    <table class="table table-dark-custom table-hover mb-0">
                        <thead>
                            <tr>
                                <th>{{ $esMensual ? 'Día' : 'Mes' }}</th>
                                <th class="text-end">Ventas</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-end">Ticket prom.</th>
                                @if($conCostos)<th class="text-end">Ganancia</th>@endif
                                <th class="text-end">Var. vs anterior</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($resumen as $fila)
                                <tr>
                                    <td class="text-white fw-bold">{{ $fila['etiqueta'] }}</td>
                                    <td class="text-end">${{ number_format($fila['total'], 0, ',', '.') }}</td>
                                    <td class="text-center">{{ $fila['cantidad'] }}</td>
                                    <td class="text-end">${{ number_format($fila['ticket'], 0, ',', '.') }}</td>
                                    @if($conCostos)
                                        <td class="text-end text-success">${{ number_format($fila['ganancia'], 0, ',', '.') }}</td>
                                    @endif
                                    <td class="text-end">
                                        @if($fila['variacion'] === null)
                                            <span class="text-muted">—</span>
                                        @else
                                            <span class="fw-bold {{ $fila['variacion'] >= 0 ? 'text-success' : 'text-danger' }}">
                                                {{ $fila['variacion'] >= 0 ? '+' : '' }}{{ number_format($fila['variacion'], 1, ',', '.') }}%
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @if($comparacionAnual)
            <div class="card-glass mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                        <h5 class="mb-0" style="font-weight: 700;">
                            <i class="bi bi-calendar-range me-2" style="color: #3498db;"></i>
                            {{ $anio }} vs {{ $comparacionAnual['anioPrevio'] }}
                        </h5>
                        <span class="badge fs-6 {{ $comparacionAnual['variacion'] >= 0 ? 'badge-stock-ok' : 'badge-stock-critico' }}">
                            {{ $comparacionAnual['variacion'] >= 0 ? '+' : '' }}{{ number_format($comparacionAnual['variacion'], 1, ',', '.') }}% anual
                        </span>
                    </div>
                    <p class="text-muted mb-3" style="font-size: 0.82rem;">Ventas mes por mes</p>
                    <div class="chart-loading text-center py-4" data-chart="ch-yoy">
                        <div class="spinner-border text-info" role="status"></div>
                    </div>
                    <div style="position: relative; height: 280px;">
                        <canvas id="ch-yoy"></canvas>
                    </div>
                </div>
            </div>
        @endif
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(function () {
    'use strict';

    // Muestra/oculta el selector de mes según el tipo de reporte.
    var tipo = document.getElementById('f-tipo');
    var wrapMes = document.getElementById('wrap-mes');
    if (tipo && wrapMes) {
        tipo.addEventListener('change', function () {
            wrapMes.style.display = tipo.value === 'mensual' ? '' : 'none';
        });
    }

    var HAY_DATOS = @json($hayDatos);
    if (!HAY_DATOS) return;

    // Si el CDN falló (offline), oculta spinners y avisa; las tablas siguen visibles.
    if (typeof Chart === 'undefined') {
        document.querySelectorAll('.chart-loading').forEach(function (el) {
            el.innerHTML = '<p class="text-muted mb-0" style="font-size:0.85rem;">Gráfico no disponible sin conexión.</p>';
        });
        return;
    }

    // Tema oscuro global de Chart.js.
    Chart.defaults.color = '#cbd5e1';
    Chart.defaults.borderColor = 'rgba(255,255,255,0.06)';
    Chart.defaults.font.family = "'Inter', system-ui, sans-serif";

    var fmtARS = new Intl.NumberFormat('es-AR', { style: 'currency', currency: 'ARS', maximumFractionDigits: 0 });
    var fmtNum = new Intl.NumberFormat('es-AR');

    var SERIES = @json($series);
    var CATEGORIAS = @json($categorias);
    var TOP_FACT = @json(array_values(array_map(fn($p) => ['producto' => $p['producto'], 'total' => $p['total'], 'unidades' => $p['unidades']], $topFacturacion)));
    var PAGOS = @json($pagos);
    var YOY = @json($comparacionAnual);
    var CON_COSTOS = @json($conCostos);
    var TOOLTIP_BG = 'rgba(15,15,30,0.95)';

    var PALETA = ['#d4a574', '#3498db', '#2ecc71', '#9b59b6', '#e74c3c', '#f39c12', '#1abc9c', '#e91e63', '#95a5a6', '#5d6d7e', '#f1948a', '#85c1e9'];

    function listo(id) {
        var loader = document.querySelector('.chart-loading[data-chart="' + id + '"]');
        if (loader) loader.style.display = 'none';
    }

    function tooltipMoneda() {
        return {
            backgroundColor: TOOLTIP_BG,
            borderColor: 'rgba(212,165,116,0.3)',
            borderWidth: 1,
            padding: 10,
            callbacks: {
                label: function (ctx) { return ' ' + fmtARS.format(ctx.parsed.y ?? ctx.parsed); }
            }
        };
    }

    // 1. Evolución de ventas (línea, principal).
    new Chart(document.getElementById('ch-evolucion'), {
        type: 'line',
        data: {
            labels: SERIES.etiquetas,
            datasets: [{
                label: 'Total vendido',
                data: SERIES.totales,
                borderColor: '#d4a574',
                backgroundColor: 'rgba(212,165,116,0.15)',
                fill: true,
                tension: 0.35,
                pointRadius: 3,
                pointBackgroundColor: '#d4a574'
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: tooltipMoneda() },
            scales: { y: { ticks: { callback: function (v) { return '$' + fmtNum.format(v); } } } }
        }
    });
    listo('ch-evolucion');

    // 2. Cantidad de ventas (barras).
    new Chart(document.getElementById('ch-cantidad'), {
        type: 'bar',
        data: {
            labels: SERIES.etiquetas,
            datasets: [{
                label: 'Ventas',
                data: SERIES.cantidades,
                backgroundColor: 'rgba(52,152,219,0.7)',
                borderRadius: 6
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { backgroundColor: TOOLTIP_BG } },
            scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
        }
    });
    listo('ch-cantidad');

    // 3. Ticket promedio (línea).
    new Chart(document.getElementById('ch-ticket'), {
        type: 'line',
        data: {
            labels: SERIES.etiquetas,
            datasets: [{
                label: 'Ticket promedio',
                data: SERIES.tickets,
                borderColor: '#2ecc71',
                backgroundColor: 'rgba(46,204,113,0.12)',
                fill: true,
                tension: 0.35,
                pointRadius: 3,
                pointBackgroundColor: '#2ecc71'
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: tooltipMoneda() },
            scales: { y: { ticks: { callback: function (v) { return '$' + fmtNum.format(v); } } } }
        }
    });
    listo('ch-ticket');

    // 4. Ventas por categoría (donut).
    new Chart(document.getElementById('ch-categorias'), {
        type: 'doughnut',
        data: {
            labels: CATEGORIAS.nombres,
            datasets: [{
                data: CATEGORIAS.totales,
                backgroundColor: PALETA,
                borderColor: '#16213e',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false, cutout: '62%',
            plugins: {
                legend: { position: 'bottom', labels: { boxWidth: 12, padding: 12 } },
                tooltip: {
                    backgroundColor: TOOLTIP_BG,
                    callbacks: {
                        label: function (ctx) {
                            var i = ctx.dataIndex;
                            return ' ' + CATEGORIAS.nombres[i] + ': ' + fmtARS.format(ctx.parsed) +
                                ' (' + CATEGORIAS.porcentajes[i] + '%)';
                        }
                    }
                }
            }
        }
    });
    listo('ch-categorias');

    // 6. Productos que más facturan (barras horizontales).
    new Chart(document.getElementById('ch-facturacion'), {
        type: 'bar',
        data: {
            labels: TOP_FACT.map(function (p) { return p.producto; }),
            datasets: [{
                label: 'Facturación',
                data: TOP_FACT.map(function (p) { return p.total; }),
                backgroundColor: 'rgba(46,204,113,0.65)',
                borderRadius: 6
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true, maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: TOOLTIP_BG,
                    callbacks: {
                        label: function (ctx) {
                            var p = TOP_FACT[ctx.dataIndex];
                            return ' ' + fmtARS.format(p.total) + ' · ' + fmtNum.format(p.unidades) + ' u.';
                        }
                    }
                }
            },
            scales: { x: { ticks: { callback: function (v) { return '$' + fmtNum.format(v); } } } }
        }
    });
    listo('ch-facturacion');

    // 7. Ganancia (línea, solo con costos confiables).
    if (CON_COSTOS && document.getElementById('ch-ganancia')) {
        new Chart(document.getElementById('ch-ganancia'), {
            type: 'line',
            data: {
                labels: SERIES.etiquetas,
                datasets: [{
                    label: 'Ganancia',
                    data: SERIES.ganancias,
                    borderColor: '#2ecc71',
                    backgroundColor: 'rgba(46,204,113,0.12)',
                    fill: true,
                    tension: 0.35,
                    pointRadius: 3,
                    pointBackgroundColor: '#2ecc71'
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false }, tooltip: tooltipMoneda() },
                scales: { y: { ticks: { callback: function (v) { return '$' + fmtNum.format(v); } } } }
            }
        });
        listo('ch-ganancia');
    }

    // 8. Medios de pago (donut).
    (function () {
        var medios = Object.keys(PAGOS);
        new Chart(document.getElementById('ch-pagos'), {
            type: 'doughnut',
            data: {
                labels: medios.map(function (m) { return m.charAt(0).toUpperCase() + m.slice(1); }),
                datasets: [{
                    data: medios.map(function (m) { return PAGOS[m].total; }),
                    backgroundColor: ['#2ecc71', '#9b59b6', '#3498db', '#f39c12', '#e74c3c'],
                    borderColor: '#16213e',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false, cutout: '62%',
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 12, padding: 12 } },
                    tooltip: {
                        backgroundColor: TOOLTIP_BG,
                        callbacks: {
                            label: function (ctx) {
                                var m = medios[ctx.dataIndex];
                                return ' ' + fmtARS.format(PAGOS[m].total) + ' · ' + PAGOS[m].cantidad + ' ventas';
                            }
                        }
                    }
                }
            }
        });
        listo('ch-pagos');
    })();

    // 10. Comparación año actual vs anterior (líneas).
    if (YOY && document.getElementById('ch-yoy')) {
        new Chart(document.getElementById('ch-yoy'), {
            type: 'line',
            data: {
                labels: SERIES.etiquetas,
                datasets: [
                    {
                        label: String(YOY.anioPrevio + 1),
                        data: SERIES.totales,
                        borderColor: '#d4a574',
                        tension: 0.35,
                        pointRadius: 3,
                        pointBackgroundColor: '#d4a574'
                    },
                    {
                        label: String(YOY.anioPrevio),
                        data: YOY.seriePrevia,
                        borderColor: '#5d6d7e',
                        borderDash: [6, 4],
                        tension: 0.35,
                        pointRadius: 2,
                        pointBackgroundColor: '#5d6d7e'
                    }
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' }, tooltip: tooltipMoneda() },
                scales: { y: { ticks: { callback: function (v) { return '$' + fmtNum.format(v); } } } }
            }
        });
        listo('ch-yoy');
    }
})();
</script>
@endpush
