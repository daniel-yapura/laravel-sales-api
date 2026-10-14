@extends('layouts.app')

@section('title', 'Productos')
@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-bold">Productos</h1>

    <form id="product-form" class="bg-white rounded-xl shadow p-4 grid sm:grid-cols-2 lg:grid-cols-5 gap-3">
        <input id="product-name" placeholder="Nombre" required class="rounded border-slate-300">
        <input id="product-price" placeholder="Precio" type="number" step="0.01" required class="rounded border-slate-300">
        <input id="product-stock" placeholder="Stock" type="number" min="0" required class="rounded border-slate-300">
        <select id="product-category" required class="rounded border-slate-300"></select>
        <input id="product-description" placeholder="Descripción" required class="rounded border-slate-300 lg:col-span-2">
        <button class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 lg:col-span-1">Guardar</button>
    </form>

    <div class="bg-white rounded-xl shadow overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-100">
                <tr>
                    <th class="px-4 py-3 text-left">ID</th>
                    <th class="px-4 py-3 text-left">Nombre</th>
                    <th class="px-4 py-3 text-left">Precio</th>
                    <th class="px-4 py-3 text-left">Stock</th>
                    <th class="px-4 py-3 text-left">Categoría</th>
                    <th class="px-4 py-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody id="product-body"></tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
let categories = [];
let editingProductId = null;

async function loadCategories() {
    categories = await window.AppApi.apiFetch('/category');
    const select = document.getElementById('product-category');
    select.innerHTML = categories.map((c) => `<option value="${c.id}">${c.name}</option>`).join('');
}

async function loadProducts() {
    const products = await window.AppApi.apiFetch('/product?per_page=100&page=0');
    const body = document.getElementById('product-body');
    body.innerHTML = products.map((p) => `
        <tr class="border-t">
            <td class="px-4 py-3">${p.id}</td>
            <td class="px-4 py-3">${p.name}</td>
            <td class="px-4 py-3">${p.price}</td>
            <td class="px-4 py-3">${p.stock ?? 0}</td>
            <td class="px-4 py-3">${categories.find((c) => c.id === p.category_id)?.name ?? p.category_id}</td>
            <td class="px-4 py-3 text-right space-x-2">
                <button data-tax="${p.id}" class="text-slate-600">IVA</button>
                <button data-edit="${p.id}" class="text-indigo-600">Editar</button>
                <button data-delete="${p.id}" class="text-red-600">Eliminar</button>
            </td>
        </tr>
    `).join('');
}

document.getElementById('product-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const payload = {
        name: document.getElementById('product-name').value,
        description: document.getElementById('product-description').value,
        price: Number(document.getElementById('product-price').value),
        stock: Number(document.getElementById('product-stock').value),
        category_id: Number(document.getElementById('product-category').value),
    };

    try {
        if (editingProductId) {
            await window.AppApi.apiFetch(`/product/${editingProductId}`, { method: 'PUT', body: payload });
            window.AppApi.showToast('Producto actualizado');
            editingProductId = null;
        } else {
            await window.AppApi.apiFetch('/product', { method: 'POST', body: payload });
            window.AppApi.showToast('Producto creado');
        }
        e.target.reset();
        await loadProducts();
    } catch (error) {
        const errors = window.AppApi.validationErrors(error);
        window.AppApi.showToast(Object.values(errors).flat()[0] || window.AppApi.errorText(error), 'error');
    }
});

document.getElementById('product-body').addEventListener('click', async (e) => {
    const deleteId = e.target.dataset.delete;
    const editId = e.target.dataset.edit;
    const taxId = e.target.dataset.tax;

    if (taxId) {
        const data = await window.AppApi.apiFetch(`/info/tax/${taxId}`);
        window.AppApi.showToast(`Precio con IVA: ${data.priceWithIVA}`);
    }

    if (deleteId) {
        if (!confirm('¿Eliminar producto?')) return;
        await window.AppApi.apiFetch(`/product/${deleteId}`, { method: 'DELETE' });
        window.AppApi.showToast('Producto eliminado');
        await loadProducts();
    }

    if (editId) {
        const products = await window.AppApi.apiFetch('/product?per_page=100&page=0');
        const product = products.find((p) => p.id == editId);
        if (!product) return;
        editingProductId = editId;
        document.getElementById('product-name').value = product.name;
        document.getElementById('product-description').value = product.description;
        document.getElementById('product-price').value = product.price;
        document.getElementById('product-stock').value = product.stock ?? 0;
        document.getElementById('product-category').value = product.category_id;
    }
});

(async () => {
    await loadCategories();
    await loadProducts();
})();
</script>
