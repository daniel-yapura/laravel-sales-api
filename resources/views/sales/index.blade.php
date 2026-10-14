@extends('layouts.app')

@section('title', 'Ventas')
@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-bold">Ventas</h1>

    <form id="sale-form" class="bg-white rounded-xl shadow p-4 space-y-4">
        <div class="grid sm:grid-cols-2 gap-3">
            <input id="sale-email" placeholder="Email del cliente" type="email" required class="rounded border-slate-300">
            <input id="sale-date" placeholder="Fecha de venta" type="datetime-local" required class="rounded border-slate-300">
        </div>

        <div class="space-y-2" id="concept-rows"></div>
        <button id="add-concept" type="button" class="text-indigo-600 hover:underline">+ Agregar concepto</button>

        <button class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Registrar venta</button>
    </form>

    <div id="sale-result" class="bg-white rounded-xl shadow p-4 hidden"></div>
</div>
@endsection

@push('scripts')
<script>
let products = [];

function conceptRow() {
    return `
        <div class="grid sm:grid-cols-2 gap-3 concept-row">
            <select class="concept-product rounded border-slate-300"></select>
            <input class="concept-quantity rounded border-slate-300" type="number" min="1" value="1" placeholder="Cantidad">
        </div>
    `;
}

function fillProductSelects() {
    document.querySelectorAll('.concept-product').forEach((select) => {
        const value = select.value;
        select.innerHTML = products.map((p) => `<option value="${p.id}">${p.name} (stock: ${p.stock ?? 0})</option>`).join('');
        if (value) select.value = value;
    });
}

function addConceptRow() {
    const container = document.getElementById('concept-rows');
    container.insertAdjacentHTML('beforeend', conceptRow());
    fillProductSelects();
}

document.getElementById('add-concept').addEventListener('click', addConceptRow);

document.getElementById('sale-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const concepts = [...document.querySelectorAll('.concept-row')].map((row) => ({
        product_id: Number(row.querySelector('.concept-product').value),
        quantity: Number(row.querySelector('.concept-quantity').value),
    }));

    const payload = {
        email: document.getElementById('sale-email').value,
        sale_date: document.getElementById('sale-date').value.replace('T', ' ') + ':00',
        concepts,
    };

    try {
        const data = await window.AppApi.apiFetch('/sale', { method: 'POST', body: payload });
        const result = document.getElementById('sale-result');
        result.classList.remove('hidden');
        result.innerHTML = `
            <h2 class="font-bold mb-2">Venta registrada</h2>
            <p>Total: <strong>${data.total}</strong></p>
            <pre class="text-xs bg-slate-100 p-3 rounded overflow-auto">${JSON.stringify(data, null, 2)}</pre>
        `;
        window.AppApi.showToast('Venta registrada correctamente');
    } catch (error) {
        window.AppApi.showToast(window.AppApi.errorText(error), 'error');
    }
});

(async () => {
    products = await window.AppApi.apiFetch('/product?per_page=100&page=0');
    addConceptRow();
})();
</script>
