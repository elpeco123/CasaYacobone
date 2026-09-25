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
                    <div class="kpi-label">Beneficio estimado</div>
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
                            <i class="bi bi-bar-chart-fill me-2" style="color: #8fbcd4;"></i>Cantidad de ventas
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
                            <i class="bi bi-receipt me-2" style="color: #9bb35f;"></i>Ticket promedio
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
                    <i class="bi bi-pie-chart-fill me-2" style="color: #cfa0c6;"></i>Ventas por categoría
                </h5>
                <p class="text-muted mb-3" style="font-size: 0.82rem;">Distribución del importe vendido</p>
                <div class="row g-3 align-items-center">
                    <div class="col-lg-5">
                        <div class="chart-loading text-center py-4" data-chart="ch-categorias">
                            <div class="spinner-border" style="color: #cfa0c6;" role="status"></div>
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
                                    <td style="color: #cdb99c;">{{ $p['categoria'] }}</td>
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
                    <i class="bi bi-cash-stack me-2" style="color: #9bb35f;"></i>Productos que más facturan · Top 10
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
            <div class="card-glass mb-4" style="border-color: rgba(155, 179, 95, 0.25);">
                <div class="card-body">
                    <h5 class="mb-1" style="font-weight: 700;">
                        <i class="bi bi-piggy-bank-fill me-2" style="color: #9bb35f;"></i>Beneficio
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
                                <tr><th>Producto</th><th class="text-end">Ventas</th><th class="text-end">Costo</th><th class="text-end">Beneficio</th><th class="text-end">Margen %</th></tr>
                            </thead>
                            <tbody>
                                @foreach($rentabilidad as $r)
                                    <tr>
                                        <td class="text-white">{{ $r['producto'] }}</td>
                                        <td class="text-end">${{ number_format($r['ventas'], 0, ',', '.') }}</td>
                                        <td class="text-end" style="color: #cdb99c;">${{ number_format($r['costo'], 0, ',', '.') }}</td>
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
                Sección de beneficio no disponible: solo el {{ number_format($coberturaCosto, 1, ',', '.') }}% de lo vendido
                tiene costo registrado. No se calcula el beneficio sin información confiable.
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

        {{-- 8b. Gastos por categoría --}}
        <div class="card-glass mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                    <h5 class="mb-0" style="font-weight: 700;">
                        <i class="bi bi-cash-coin me-2" style="color: var(--cy-accent);"></i>Gastos por categoría
                    </h5>
                    <span class="badge fs-6" style="background: rgba(208, 85, 58, 0.15); color: #ff8fa3; border: 1px solid rgba(208, 85, 58, 0.3);">
                        Total: ${{ number_format($totalGastos, 0, ',', '.') }}
                    </span>
                </div>
                <p class="text-muted mb-3" style="font-size: 0.82rem;">En qué se va el dinero de la caja · {{ $esMensual ? 'mes' : 'año' }} seleccionado</p>
                @if($totalGastos > 0)
                    <div class="row g-3 align-items-center">
                        <div class="col-lg-5">
                            <div class="chart-loading text-center py-4" data-chart="ch-gastos">
                                <div class="spinner-border text-danger" role="status"></div>
                            </div>
                            <div style="position: relative; height: 240px;">
                                <canvas id="ch-gastos"></canvas>
                            </div>
                        </div>
                        <div class="col-lg-7">
                            <div class="table-responsive">
                                <table class="table table-dark-custom table-hover mb-0">
                                    <thead>
                                        <tr><th>Categoría</th><th class="text-end">Importe</th><th class="text-center">Gastos</th><th class="text-end">%</th></tr>
                                    </thead>
                                    <tbody>
                                        @foreach($gastosCategoria as $nombre => $d)
                                            <tr>
                                                <td class="text-white">{{ $nombre }}</td>
                                                <td class="text-end fw-bold text-danger">
                                                    ${{ number_format($d['total'], 0, ',', '.') }}
                                                </td>
                                                <td class="text-center">{{ number_format($d['cantidad'], 0, ',', '.') }}</td>
                                                <td class="text-end">{{ number_format($d['total'] / $totalGastos * 100, 1, ',', '.') }}%</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @else
                    <p class="text-center text-muted py-3 mb-0">
                        <i class="bi bi-inbox me-1"></i>Sin gastos registrados en este período.
                    </p>
                @endif
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
                                @if($conCostos)<th class="text-end">Beneficio</th>@endif
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
                            <i class="bi bi-calendar-range me-2" style="color: #8fbcd4;"></i>
                            {{ $anio }} vs {{ $comparacionAnual['anioPrevio'] }}
                        </h5>
                        <span class="badge fs-6 {{ $comparacionAnual['variacion'] >= 0 ? 'badge-stock-ok' : 'badge-stock-critico' }}">
                            {{ $comparacionAnual['variacion'] >= 0 ? '+' : '' }}{{ number_format($comparacionAnual['variacion'], 1, ',', '.') }}% vs. {{ $comparacionAnual['anioPrevio'] }}
                        </span>
                    </div>
                    <p class="text-muted mb-3" style="font-size: 0.82rem;">Ventas mes a mes, comparando el mismo tramo del año</p>
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
    Chart.defaults.color = '#cdb99c';
    Chart.defaults.borderColor = 'rgba(255,255,255,0.06)';
    Chart.defaults.font.family = "'Work Sans', system-ui, sans-serif";

    var fmtARS = new Intl.NumberFormat('es-AR', { style: 'currency', currency: 'ARS', maximumFractionDigits: 0 });
    var fmtNum = new Intl.NumberFormat('es-AR');

    var SERIES = @json($series);
    var CATEGORIAS = @json($categorias);
    var TOP_FACT = @json($topFacturacionChart);
    var PAGOS = @json($pagos);
    var GASTOS_CAT = @json($gastosCategoria);
    var YOY = @json($comparacionAnual);
    var CON_COSTOS = @json($conCostos);
    var TOOLTIP_BG = 'rgba(26, 17, 12, 0.95)';

    // Paleta de 8 colores, en este orden fijo. Verificada sobre el fondo oscuro:
    // todos se distinguen entre sí, también con daltonismo, y no se leen grises.
    var PALETA = ['#b77610', '#0098b7', '#58994a', '#b9649f', '#8b8c14', '#5685d4', '#c86556', '#9372c8'];

    // Cada forma de pago tiene su color fijo, no depende del orden en que aparezca.
    var COLOR_PAGO = {
        efectivo: '#58994a',
        debito: '#0098b7',
        credito: '#b9649f',
        factura: '#5685d4',
        cuenta_corriente: '#b77610',
        tarjeta: '#b9649f'
    };

    // Una torta con más de 8 porciones no se lee: las más chicas se juntan en
    // "Otros". La tabla de al lado sigue mostrando todas, una por una.
    var MAX_PORCIONES = 7;

    function agrupar(nombres, valores) {
        if (nombres.length <= MAX_PORCIONES + 1) {
            return { nombres: nombres, valores: valores };
        }
        var resto = valores.slice(MAX_PORCIONES).reduce(function (a, b) { return a + b; }, 0);

        return {
            nombres: nombres.slice(0, MAX_PORCIONES).concat(['Otros']),
            valores: valores.slice(0, MAX_PORCIONES).concat([resto])
        };
    }

    function listo(id) {
        var loader = document.querySelector('.chart-loading[data-chart="' + id + '"]');
        if (loader) loader.style.display = 'none';
    }

    function tooltipMoneda() {
        return {
            backgroundColor: TOOLTIP_BG,
            borderColor: 'rgba(212, 154, 82, 0.3)',
            borderWidth: 1,
            padding: 10,
            callbacks: {
                label: function (ctx) { return ' ' + fmtARS.format(ctx.parsed.y ?? ctx.parsed); }
            }
        };
    }

    // Con el dedo o el mouse alcanza con estar sobre la columna: no hay que
    // acertarle justo al punto.
    var PORCOLUMNA = { mode: 'index', intersect: false };

    // 1. Evolución de ventas (línea, principal).
    new Chart(document.getElementById('ch-evolucion'), {
        type: 'line',
        data: {
            labels: SERIES.etiquetas,
            datasets: [{
                label: 'Total vendido',
                data: SERIES.totales,
                borderColor: '#d49a52',
                backgroundColor: 'rgba(212, 154, 82, 0.15)',
                fill: true,
                tension: 0,
                pointRadius: 4,
                pointBackgroundColor: '#d49a52'
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            interaction: PORCOLUMNA,
            plugins: { legend: { display: false }, tooltip: tooltipMoneda() },
            scales: { y: { beginAtZero: true, ticks: { callback: function (v) { return '$' + fmtNum.format(v); } } } }
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
                backgroundColor: 'rgba(143, 188, 212, 0.7)',
                borderRadius: 4
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
                borderColor: '#9bb35f',
                backgroundColor: 'rgba(155, 179, 95, 0.12)',
                fill: true,
                tension: 0,
                pointRadius: 4,
                pointBackgroundColor: '#9bb35f'
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            interaction: PORCOLUMNA,
            plugins: { legend: { display: false }, tooltip: tooltipMoneda() },
            scales: { y: { beginAtZero: true, ticks: { callback: function (v) { return '$' + fmtNum.format(v); } } } }
        }
    });
    listo('ch-ticket');

    // 4. Ventas por categoría (donut).
    var CATS = agrupar(CATEGORIAS.nombres, CATEGORIAS.totales);
    new Chart(document.getElementById('ch-categorias'), {
        type: 'doughnut',
        data: {
            labels: CATS.nombres,
            datasets: [{
                data: CATS.valores,
                backgroundColor: PALETA.slice(0, CATS.valores.length),
                borderColor: '#2b1d15',
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
                            var total = CATS.valores.reduce(function (a, b) { return a + b; }, 0);
                            var porcentaje = total > 0 ? (ctx.parsed / total * 100).toFixed(1) : '0,0';

                            return ' ' + CATS.nombres[ctx.dataIndex] + ': ' + fmtARS.format(ctx.parsed) +
                                ' (' + porcentaje + '%)';
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
                backgroundColor: 'rgba(155, 179, 95, 0.65)',
                borderRadius: 4
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

    // 7. Beneficio por bucket (barras: el signo se lee de una).
    if (CON_COSTOS && document.getElementById('ch-ganancia')) {
        new Chart(document.getElementById('ch-ganancia'), {
            type: 'bar',
            data: {
                labels: SERIES.etiquetas,
                datasets: [{
                    label: 'Beneficio',
                    data: SERIES.ganancias,
                    backgroundColor: SERIES.ganancias.map(function (v) {
                        return v < 0 ? 'rgba(208, 85, 58, 0.75)' : 'rgba(155, 179, 95, 0.7)';
                    }),
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                interaction: PORCOLUMNA,
                plugins: { legend: { display: false }, tooltip: tooltipMoneda() },
                scales: { y: { beginAtZero: true, ticks: { callback: function (v) { return '$' + fmtNum.format(v); } } } }
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
                    // Cada medio conserva su color de siempre (el mismo de los chips de venta).
                    backgroundColor: medios.map(function (m, i) { return COLOR_PAGO[m] || PALETA[i]; }),
                    borderColor: '#2b1d15',
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

    // 8b. Gastos por categoría (donut, solo si hay canvas).
    (function () {
        var canvas = document.getElementById('ch-gastos');
        if (!canvas) return;
        var cats = Object.keys(GASTOS_CAT);
        var gastos = agrupar(cats, cats.map(function (c) { return GASTOS_CAT[c].total; }));
        new Chart(canvas, {
            type: 'doughnut',
            data: {
                labels: gastos.nombres,
                datasets: [{
                    data: gastos.valores,
                    backgroundColor: PALETA.slice(0, gastos.valores.length),
                    borderColor: '#2b1d15',
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
                                var c = gastos.nombres[ctx.dataIndex];
                                var d = GASTOS_CAT[c];

                                return d
                                    ? ' ' + c + ': ' + fmtARS.format(d.total) + ' · ' + d.cantidad + ' gastos'
                                    : ' ' + c + ': ' + fmtARS.format(gastos.valores[ctx.dataIndex]);
                            }
                        }
                    }
                }
            }
        });
        listo('ch-gastos');
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
                        borderColor: '#d49a52',
                        tension: 0,
                        pointRadius: 4,
                        pointBackgroundColor: '#d49a52'
                    },
                    {
                        label: String(YOY.anioPrevio),
                        data: YOY.seriePrevia,
                        borderColor: '#7d6a57',
                        borderDash: [6, 4],
                        tension: 0,
                        pointRadius: 2,
                        pointBackgroundColor: '#7d6a57'
                    }
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                interaction: PORCOLUMNA,
                plugins: { legend: { position: 'bottom' }, tooltip: tooltipMoneda() },
                scales: { y: { beginAtZero: true, ticks: { callback: function (v) { return '$' + fmtNum.format(v); } } } }
            }
        });
        listo('ch-yoy');
    }
})();
</script>
@endpush
