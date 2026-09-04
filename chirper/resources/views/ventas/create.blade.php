@extends('layouts.app')

@section('title', 'Nueva Venta')

@push('styles')
<style>
    .custom-search-results {
        max-height: 280px !important;
        overflow-y: auto !important;
        overflow-x: hidden !important;
        scrollbar-width: thin;
        scrollbar-color: var(--cy-gold) rgba(15, 23, 42, 0.8);
    }
    .custom-search-results::-webkit-scrollbar {
        width: 8px;
    }
    .custom-search-results::-webkit-scrollbar-track {
        background: rgba(15, 23, 42, 0.8);
        border-radius: 4px;
    }
    .custom-search-results::-webkit-scrollbar-thumb {
        background: var(--cy-gold);
        border-radius: 4px;
    }
    .custom-search-results::-webkit-scrollbar-thumb:hover {
        background: var(--cy-gold-light);
    }
</style>
@endpush

@section('content')
<div class="fade-in">
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <div>
            <h1><i class="bi bi-cart-plus-fill me-2" style="color: var(--cy-accent);"></i>Registrar Nueva Venta</h1>
        </div>
        <a href="{{ route('ventas.index') }}" class="btn btn-glass">
            <i class="bi bi-arrow-left me-1"></i>Volver a Ventas
        </a>
    </div>

    <form method="POST" action="{{ route('ventas.store') }}" id="ventaForm">
        @csrf

        <div class="row g-4">
            {{-- Left Column: Product Search & Selected Items --}}
            <div class="col-lg-8">

                {{-- Buscador Rápido de Productos --}}
                <div class="card-glass mb-4" style="background: rgba(15, 52, 96, 0.4); border: 1px solid rgba(212, 165, 116, 0.25);">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-search text-warning fs-5"></i>
                            <h6 class="mb-0 fw-bold text-light">Buscador Rápido de Productos</h6>
                            <span class="badge bg-gold-light text-dark ms-auto" style="font-size: 0.75rem;">
                                {{ $productos->count() }} disponibles
                            </span>
                        </div>
                        <div class="position-relative">
                            <input type="text" id="quickProductSearch" class="form-control form-control-dark ps-5"
                                   placeholder="Escribí el nombre, marca o categoría del producto (ej: Remera, Nike)..."
                                   autocomplete="off">
                            <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                        </div>

                        {{-- Live Search Results dropdown list --}}
                        <div id="searchResultsList" class="mt-2 rounded-3 shadow-lg custom-search-results"
                             style="display: none; background: rgba(15, 20, 40, 0.98); border: 1px solid var(--cy-gold);">
                            {{-- Results populated via JS --}}
                        </div>
                    </div>
                </div>

                {{-- Tabla de Productos Seleccionados en la Venta --}}
                <div class="card-glass">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0 fw-bold">
                                <i class="bi bi-bag-check-fill me-2" style="color: var(--cy-gold);"></i>
                                Items en esta Venta
                            </h5>
                            <button type="button" class="btn btn-gold btn-sm" id="btnAgregarItem">
                                <i class="bi bi-plus-circle-fill me-1"></i>Agregar Fila Manual
                            </button>
                        </div>

                        <div id="itemsContainer">
                            {{-- Dynamic rows will be inserted here --}}
                        </div>

                        <div id="emptyMessage" class="text-center py-5">
                            <i class="bi bi-cart-x" style="font-size: 3rem; color: #94a3b8; opacity: 0.7;"></i>
                            <h6 class="text-white mt-3 mb-1 fw-bold">No hay productos agregados a la venta</h6>
                            <p class="small" style="color: #cbd5e1;">Buscá un producto arriba o tocá en <strong class="text-white">"Agregar Fila Manual"</strong> para comenzar.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column: Resumen y Confirmación --}}
            <div class="col-lg-4">
                <div class="card-glass" style="position: sticky; top: 90px; background: rgba(22, 33, 62, 0.85); border: 1px solid rgba(212, 165, 116, 0.25);">
                    <div class="card-body">
                        <h5 class="mb-3 fw-bold text-white">
                            <i class="bi bi-receipt-cutoff me-2" style="color: var(--cy-gold);"></i>
                            Resumen de Venta
                        </h5>

                        <div class="d-flex justify-content-between align-items-center mb-2" style="font-size: 0.95rem;">
                            <span style="color: #e2e8f0; font-weight: 500;">Variedad de Productos:</span>
                            <span id="totalItems" class="fw-bold text-white px-2 py-0.5 rounded" style="background: rgba(255,255,255,0.1); min-width: 28px; text-align: center;">0</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3" style="font-size: 0.95rem;">
                            <span style="color: #e2e8f0; font-weight: 500;">Total de Unidades:</span>
                            <span id="totalUnidades" class="fw-bold px-2 py-0.5 rounded" style="background: rgba(56, 189, 248, 0.2); color: #38bdf8; min-width: 28px; text-align: center;">0</span>
                        </div>

                        <div class="mb-3">
                            <label for="tipo_pago" class="form-label text-light fw-semibold" style="color: #e2e8f0 !important;">Forma de Pago *</label>
                            <select name="tipo_pago" id="tipo_pago" class="form-select form-select-dark @error('tipo_pago') is-invalid @enderror" required style="font-weight: 600;">
                                <option value="efectivo" {{ old('tipo_pago') == 'efectivo' ? 'selected' : '' }}>💵 Efectivo</option>
                                <option value="tarjeta" {{ old('tipo_pago') == 'tarjeta' ? 'selected' : '' }}>💳 Tarjeta</option>
                                <option value="factura" {{ old('tipo_pago') == 'factura' ? 'selected' : '' }}>📄 Factura</option>
                            </select>
                            @error('tipo_pago')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="descuento_porcentaje" class="form-label d-flex justify-content-between align-items-center fw-semibold" style="color: #e2e8f0 !important;">
                                <span>Descuento (%)</span>
                                <span class="badge bg-warning text-dark fw-bold" id="descuentoTag" style="font-size: 0.8rem;">0%</span>
                            </label>
                            <div class="input-group">
                                <input type="number" name="descuento_porcentaje" id="descuento_porcentaje"
                                       class="form-control form-control-dark @error('descuento_porcentaje') is-invalid @enderror"
                                       min="0" max="100" step="any" value="{{ old('descuento_porcentaje', 0) }}" placeholder="Ej: 5, 10, 15, 20">
                                <span class="input-group-text bg-dark text-warning border-secondary fw-bold">%</span>
                            </div>
                            @error('descuento_porcentaje')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr style="border-color: rgba(255,255,255,0.15); margin: 1.2rem 0;">

                        <div class="d-flex justify-content-between align-items-center mb-2" style="font-size: 0.95rem;">
                            <span style="color: #e2e8f0; font-weight: 600;">Subtotal:</span>
                            <span id="subtotalMonto" class="fw-bold text-white fs-6">$0</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3" style="font-size: 0.95rem;">
                            <span style="color: #e2e8f0; font-weight: 600;">Descuento Aplicado:</span>
                            <span id="descuentoMonto" class="fw-bold fs-6" style="color: #ff6b6b;">-$0</span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3 p-3 rounded-3" style="background: rgba(212, 165, 116, 0.12); border: 1px solid rgba(212, 165, 116, 0.3);">
                            <span class="text-white" style="font-size: 1.15rem; font-weight: 700;">Total a Cobrar:</span>
                            <span id="totalMonto" style="font-size: 1.85rem; font-weight: 800; color: var(--cy-gold); text-shadow: 0 0 14px rgba(212,165,116,0.35);">$0</span>
                        </div>

                        <button type="submit" class="btn btn-accent w-100 py-2.5 text-uppercase fw-bold" id="btnRegistrar" disabled style="letter-spacing: 0.5px;">
                            <i class="bi bi-check-circle-fill me-2"></i>Confirmar Venta
                        </button>
                        <a href="{{ route('ventas.index') }}" class="btn btn-glass w-100 mt-2 text-light">
                            Cancelar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- Plantilla de Fila de Producto --}}
<template id="itemTemplate">
    <div class="item-row mb-3 p-3 rounded-3 fade-in" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.12);">
        <div class="row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label text-light fw-medium" style="color: #e2e8f0 !important;">Seleccionar Producto</label>
                <select class="form-select form-select-dark item-producto" name="items[__INDEX__][producto_id]" required>
                    <option value="">Buscar o seleccionar...</option>
                    @foreach($productos as $prod)
                        <option value="{{ $prod->id }}"
                                data-precio="{{ $prod->precio_venta }}"
                                data-stock="{{ $prod->stock }}"
                                data-nombre="{{ $prod->nombre }}"
                                data-marca="{{ $prod->marca }}"
                                data-categoria="{{ $prod->categoria->nombre ?? '' }}">
                            {{ $prod->nombre }} ({{ $prod->marca }}) — ${{ number_format($prod->precio_venta, 0, ',', '.') }} [Stock: {{ $prod->stock }}]
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label text-light fw-medium" style="color: #e2e8f0 !important;">Cantidad</label>
                <input type="number" class="form-control form-control-dark item-cantidad"
                       name="items[__INDEX__][cantidad]" min="1" value="1" required>
            </div>
            <div class="col-md-2">
                <label class="form-label text-light fw-medium" style="color: #e2e8f0 !important;">Precio Unit.</label>
                <input type="text" class="form-control form-control-dark item-precio text-end fw-bold" readonly
                       style="color: var(--cy-gold);">
            </div>
            <div class="col-md-2">
                <label class="form-label text-light fw-medium" style="color: #e2e8f0 !important;">Subtotal</label>
                <input type="text" class="form-control form-control-dark item-subtotal text-end" readonly
                       style="color: var(--cy-gold-light); font-weight: 700; font-size: 1.05rem;">
            </div>
            <div class="col-md-1 text-center">
                <button type="button" class="btn btn-sm btn-remove-item" title="Quitar item"
                        style="background: rgba(231,76,60,0.18); border: 1px solid rgba(231,76,60,0.35); color: #ff6b6b; border-radius: 8px; padding: 0.45rem 0.65rem;">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>
        <div class="item-stock-info mt-2 d-flex justify-content-between align-items-center" style="font-size: 0.82rem; color: #cbd5e1; display: none;">
            <span><i class="bi bi-info-circle me-1 text-info"></i>Stock disponible: <strong class="item-stock-display text-white">0</strong></span>
            <span class="item-categoria-display text-gold-light fw-semibold"></span>
        </div>
    </div>
</template>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Datos de productos para el buscador inteligente
        const productosData = [
            @foreach($productos as $prod)
            {
                id: {{ $prod->id }},
                nombre: @json($prod->nombre),
                marca: @json($prod->marca),
                categoria: @json($prod->categoria->nombre ?? ''),
                precio: {{ $prod->precio_venta }},
                stock: {{ $prod->stock }}
            },
            @endforeach
        ];

        let itemIndex = 0;
        const container = document.getElementById('itemsContainer');
        const template = document.getElementById('itemTemplate');
        const emptyMessage = document.getElementById('emptyMessage');
        const btnRegistrar = document.getElementById('btnRegistrar');
        const quickSearch = document.getElementById('quickProductSearch');
        const resultsList = document.getElementById('searchResultsList');

        // Evento para agregar fila vacía
        document.getElementById('btnAgregarItem').addEventListener('click', () => addItem());

        // Buscador Inteligente en Tiempo Real
        quickSearch.addEventListener('input', function() {
            const query = this.value.trim().toLowerCase();
            if (query.length < 1) {
                resultsList.style.display = 'none';
                resultsList.innerHTML = '';
                return;
            }

            const matches = productosData.filter(p =>
                p.nombre.toLowerCase().includes(query) ||
                p.marca.toLowerCase().includes(query) ||
                p.categoria.toLowerCase().includes(query)
            );

            if (matches.length === 0) {
                resultsList.innerHTML = `
                    <div class="p-3 text-center" style="color: #cbd5e1;">
                        <i class="bi bi-search me-1"></i>No se encontraron productos con "${query}"
                    </div>
                `;
                resultsList.style.display = 'block';
                return;
            }

            resultsList.innerHTML = matches.map(p => `
                <div class="search-item-result p-2 px-3 border-bottom border-secondary d-flex justify-content-between align-items-center"
                     style="cursor: pointer; transition: background 0.2s;"
                     data-id="${p.id}"
                     onmouseover="this.style.background='rgba(212, 165, 116, 0.15)'"
                     onmouseout="this.style.background='transparent'">
                    <div>
                        <strong class="text-white">${p.nombre}</strong>
                        <div class="small" style="color: #cbd5e1;">${p.marca} · <span class="text-info">${p.categoria}</span></div>
                    </div>
                    <div class="text-end ms-3">
                        <div class="fw-bold" style="color: var(--cy-gold); font-size: 1rem;">$${p.precio.toLocaleString('es-AR')}</div>
                        <span class="badge bg-secondary text-white" style="font-size: 0.72rem;">Stock: ${p.stock}</span>
                        <button type="button" class="btn btn-gold btn-sm ms-2 py-0 px-2 btn-add-fast" style="font-size: 0.75rem;">
                            <i class="bi bi-plus-lg"></i> Agregar
                        </button>
                    </div>
                </div>
            `).join('');

            resultsList.style.display = 'block';
        });

        // Click handler para agregar desde los resultados de búsqueda
        resultsList.addEventListener('click', function(e) {
            const row = e.target.closest('.search-item-result');
            if (row) {
                const prodId = parseInt(row.dataset.id);
                addProductToCart(prodId);
                quickSearch.value = '';
                resultsList.style.display = 'none';
                quickSearch.focus();
            }
        });

        // Ocultar la lista de resultados al hacer click afuera
        document.addEventListener('click', function(e) {
            if (!quickSearch.contains(e.target) && !resultsList.contains(e.target)) {
                resultsList.style.display = 'none';
            }
        });

        // Agregar un producto directamente seleccionándolo por ID
        function addProductToCart(productId) {
            // Verificar si el producto ya existe en la lista de items
            let existingRow = null;
            container.querySelectorAll('.item-row').forEach(r => {
                const select = r.querySelector('.item-producto');
                if (parseInt(select.value) === productId) {
                    existingRow = r;
                }
            });

            if (existingRow) {
                // Incrementar cantidad
                const cantInput = existingRow.querySelector('.item-cantidad');
                const maxStock = parseInt(existingRow.querySelector('.item-producto').options[existingRow.querySelector('.item-producto').selectedIndex].dataset.stock);
                let currentVal = parseInt(cantInput.value) || 0;
                if (currentVal < maxStock) {
                    cantInput.value = currentVal + 1;
                    updateItemRow(existingRow);
                    updateTotal();
                }
            } else {
                // Crear nueva fila con el producto pre-seleccionado
                const newRow = addItem(productId);
            }
        }

        function addItem(selectedProductId = null) {
            const html = template.innerHTML.replace(/__INDEX__/g, itemIndex);
            const div = document.createElement('div');
            div.innerHTML = html;
            const row = div.firstElementChild;
            container.appendChild(row);
            itemIndex++;

            emptyMessage.style.display = 'none';

            const select = row.querySelector('.item-producto');
            const cantidad = row.querySelector('.item-cantidad');
            const removeBtn = row.querySelector('.btn-remove-item');

            if (selectedProductId) {
                select.value = selectedProductId;
                updateItemRow(row);
            }

            select.addEventListener('change', function() {
                updateItemRow(row);
                updateTotal();
            });

            cantidad.addEventListener('input', function() {
                const selected = select.options[select.selectedIndex];
                if (selected && selected.value) {
                    const maxStock = parseInt(selected.dataset.stock);
                    if (parseInt(this.value) > maxStock) {
                        this.value = maxStock;
                    }
                    if (parseInt(this.value) < 1) {
                        this.value = 1;
                    }
                }
                updateItemRow(row);
                updateTotal();
            });

            removeBtn.addEventListener('click', function() {
                row.remove();
                updateTotal();
                if (container.children.length === 0) {
                    emptyMessage.style.display = 'block';
                }
            });

            updateTotal();
            return row;
        }

        function updateItemRow(row) {
            const select = row.querySelector('.item-producto');
            const cantidad = row.querySelector('.item-cantidad');
            const precioInput = row.querySelector('.item-precio');
            const subtotalInput = row.querySelector('.item-subtotal');
            const stockInfo = row.querySelector('.item-stock-info');
            const stockDisplay = row.querySelector('.item-stock-display');
            const catDisplay = row.querySelector('.item-categoria-display');

            const selected = select.options[select.selectedIndex];

            if (selected && selected.value) {
                const precio = parseFloat(selected.dataset.precio);
                const stock = parseInt(selected.dataset.stock);
                const cant = parseInt(cantidad.value) || 1;
                const cat = selected.dataset.categoria;

                cantidad.max = stock;
                precioInput.value = '$' + precio.toLocaleString('es-AR');
                subtotalInput.value = '$' + (precio * cant).toLocaleString('es-AR');
                stockInfo.style.display = 'flex';
                stockDisplay.textContent = stock;
                if (catDisplay) catDisplay.textContent = cat;
            } else {
                precioInput.value = '';
                subtotalInput.value = '';
                stockInfo.style.display = 'none';
            }
        }

        const descuentoInput = document.getElementById('descuento_porcentaje');
        if (descuentoInput) {
            descuentoInput.addEventListener('input', function() {
                if (parseFloat(this.value) > 100) this.value = 100;
                if (parseFloat(this.value) < 0) this.value = 0;
                updateTotal();
            });
        }

        function updateTotal() {
            let subtotalSum = 0;
            let totalItems = 0;
            let totalUnidades = 0;

            container.querySelectorAll('.item-row').forEach(function(row) {
                const select = row.querySelector('.item-producto');
                const cantidad = row.querySelector('.item-cantidad');
                const selected = select.options[select.selectedIndex];

                if (selected && selected.value) {
                    const precio = parseFloat(selected.dataset.precio);
                    const cant = parseInt(cantidad.value) || 0;
                    subtotalSum += precio * cant;
                    totalItems++;
                    totalUnidades += cant;
                }
            });

            let descPorcentaje = parseFloat(descuentoInput ? descuentoInput.value : 0) || 0;
            if (descPorcentaje < 0) descPorcentaje = 0;
            if (descPorcentaje > 100) descPorcentaje = 100;

            const descMonto = Math.round((subtotalSum * (descPorcentaje / 100)) * 100) / 100;
            const totalMonto = Math.max(0, Math.round((subtotalSum - descMonto) * 100) / 100);

            document.getElementById('totalItems').textContent = totalItems;
            document.getElementById('totalUnidades').textContent = totalUnidades;
            document.getElementById('subtotalMonto').textContent = '$' + subtotalSum.toLocaleString('es-AR', {minimumFractionDigits: 0, maximumFractionDigits: 2});
            document.getElementById('descuentoTag').textContent = descPorcentaje + '%';
            document.getElementById('descuentoMonto').textContent = '-$' + descMonto.toLocaleString('es-AR', {minimumFractionDigits: 0, maximumFractionDigits: 2});
            document.getElementById('totalMonto').textContent = '$' + totalMonto.toLocaleString('es-AR', {minimumFractionDigits: 0, maximumFractionDigits: 2});
            btnRegistrar.disabled = totalItems === 0;
        }
    });
</script>
@endpush
@endsection
