@extends('layouts.app')

@section('title', 'Nueva Venta')

@section('content')
<div class="fade-in">
    <div class="page-header">
        <h1><i class="bi bi-cart-plus-fill me-2" style="color: var(--cy-accent);"></i>Nueva Venta</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('ventas.index') }}">Ventas</a></li>
                <li class="breadcrumb-item active">Nueva Venta</li>
            </ol>
        </nav>
    </div>

    <form method="POST" action="{{ route('ventas.store') }}" id="ventaForm">
        @csrf

        <div class="row g-4">
            {{-- Items --}}
            <div class="col-lg-8">
                <div class="card-glass">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0" style="font-weight: 700;">
                                <i class="bi bi-bag-fill me-2" style="color: var(--cy-gold);"></i>
                                Productos de la Venta
                            </h5>
                            <button type="button" class="btn btn-gold btn-sm" id="btnAgregarItem">
                                <i class="bi bi-plus-lg me-1"></i>Agregar Producto
                            </button>
                        </div>

                        <div id="itemsContainer">
                            {{-- Item rows will be added here --}}
                        </div>

                        <div id="emptyMessage" class="text-center py-4">
                            <i class="bi bi-cart-x" style="font-size: 2.5rem; color: var(--cy-text-muted);"></i>
                            <p class="text-muted mt-2 mb-0">Agregá productos a la venta usando el botón de arriba.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Summary --}}
            <div class="col-lg-4">
                <div class="card-glass" style="position: sticky; top: 80px;">
                    <div class="card-body">
                        <h5 class="mb-3" style="font-weight: 700;">
                            <i class="bi bi-receipt me-2" style="color: var(--cy-gold);"></i>
                            Resumen
                        </h5>

                        <div class="d-flex justify-content-between mb-2" style="font-size: 0.9rem;">
                            <span class="text-muted">Productos:</span>
                            <span id="totalItems" class="fw-bold">0</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2" style="font-size: 0.9rem;">
                            <span class="text-muted">Unidades:</span>
                            <span id="totalUnidades" class="fw-bold">0</span>
                        </div>

                        <hr style="border-color: var(--cy-border);">

                        <div class="d-flex justify-content-between align-items-center">
                            <span style="font-size: 1.1rem; font-weight: 600;">Total:</span>
                            <span id="totalMonto" style="font-size: 1.6rem; font-weight: 800; color: var(--cy-gold);">$0</span>
                        </div>

                        <hr style="border-color: var(--cy-border);">

                        <button type="submit" class="btn btn-accent w-100 py-2" id="btnRegistrar" disabled>
                            <i class="bi bi-check-circle-fill me-2"></i>Registrar Venta
                        </button>
                        <a href="{{ route('ventas.index') }}" class="btn btn-glass w-100 mt-2">
                            <i class="bi bi-x-lg me-1"></i>Cancelar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- Item Row Template --}}
<template id="itemTemplate">
    <div class="item-row mb-3 p-3" style="background: rgba(255,255,255,0.02); border: 1px solid var(--cy-border); border-radius: 12px;">
        <div class="row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label">Producto</label>
                <select class="form-select form-select-dark item-producto" name="items[__INDEX__][producto_id]" required>
                    <option value="">Seleccionar producto...</option>
                    @foreach($productos as $prod)
                        <option value="{{ $prod->id }}"
                                data-precio="{{ $prod->precio_venta }}"
                                data-stock="{{ $prod->stock }}"
                                data-nombre="{{ $prod->nombre }}">
                            {{ $prod->nombre }} — ${{ number_format($prod->precio_venta, 0, ',', '.') }} (Stock: {{ $prod->stock }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Cantidad</label>
                <input type="number" class="form-control form-control-dark item-cantidad"
                       name="items[__INDEX__][cantidad]" min="1" value="1" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Precio Unit.</label>
                <input type="text" class="form-control form-control-dark item-precio" readonly
                       style="color: var(--cy-gold); font-weight: 600;">
            </div>
            <div class="col-md-2">
                <label class="form-label">Subtotal</label>
                <input type="text" class="form-control form-control-dark item-subtotal" readonly
                       style="color: var(--cy-gold); font-weight: 700;">
            </div>
            <div class="col-md-1 text-center">
                <button type="button" class="btn btn-sm btn-remove-item"
                        style="background: rgba(231,76,60,0.15); border: 1px solid rgba(231,76,60,0.3); color: #e74c3c; border-radius: 8px; padding: 0.45rem 0.65rem;">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>
        <div class="item-stock-info mt-2" style="font-size: 0.78rem; color: var(--cy-text-muted); display: none;">
            <i class="bi bi-info-circle me-1"></i>Stock disponible: <span class="item-stock-display">0</span>
        </div>
    </div>
</template>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let itemIndex = 0;
        const container = document.getElementById('itemsContainer');
        const template = document.getElementById('itemTemplate');
        const emptyMessage = document.getElementById('emptyMessage');
        const btnRegistrar = document.getElementById('btnRegistrar');

        // Add first item
        addItem();

        document.getElementById('btnAgregarItem').addEventListener('click', addItem);

        function addItem() {
            const html = template.innerHTML.replace(/__INDEX__/g, itemIndex);
            const div = document.createElement('div');
            div.innerHTML = html;
            const row = div.firstElementChild;
            container.appendChild(row);
            itemIndex++;

            emptyMessage.style.display = 'none';

            // Event listeners
            const select = row.querySelector('.item-producto');
            const cantidad = row.querySelector('.item-cantidad');
            const removeBtn = row.querySelector('.btn-remove-item');

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
        }

        function updateItemRow(row) {
            const select = row.querySelector('.item-producto');
            const cantidad = row.querySelector('.item-cantidad');
            const precioInput = row.querySelector('.item-precio');
            const subtotalInput = row.querySelector('.item-subtotal');
            const stockInfo = row.querySelector('.item-stock-info');
            const stockDisplay = row.querySelector('.item-stock-display');

            const selected = select.options[select.selectedIndex];

            if (selected && selected.value) {
                const precio = parseFloat(selected.dataset.precio);
                const stock = parseInt(selected.dataset.stock);
                const cant = parseInt(cantidad.value) || 1;

                cantidad.max = stock;
                precioInput.value = '$' + precio.toLocaleString('es-AR');
                subtotalInput.value = '$' + (precio * cant).toLocaleString('es-AR');
                stockInfo.style.display = 'block';
                stockDisplay.textContent = stock;
            } else {
                precioInput.value = '';
                subtotalInput.value = '';
                stockInfo.style.display = 'none';
            }
        }

        function updateTotal() {
            let totalMonto = 0;
            let totalItems = 0;
            let totalUnidades = 0;

            container.querySelectorAll('.item-row').forEach(function(row) {
                const select = row.querySelector('.item-producto');
                const cantidad = row.querySelector('.item-cantidad');
                const selected = select.options[select.selectedIndex];

                if (selected && selected.value) {
                    const precio = parseFloat(selected.dataset.precio);
                    const cant = parseInt(cantidad.value) || 0;
                    totalMonto += precio * cant;
                    totalItems++;
                    totalUnidades += cant;
                }
            });

            document.getElementById('totalItems').textContent = totalItems;
            document.getElementById('totalUnidades').textContent = totalUnidades;
            document.getElementById('totalMonto').textContent = '$' + totalMonto.toLocaleString('es-AR');
            btnRegistrar.disabled = totalItems === 0;
        }
    });
</script>
@endpush
@endsection
