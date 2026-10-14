@extends('layouts.app')

@section('title', 'Categorías')
@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold">Categorías</h1>
    </div>

    <form id="category-form" class="bg-white rounded-xl shadow p-4 flex flex-col sm:flex-row gap-3">
        <input id="category-name" placeholder="Nombre de la categoría" required class="flex-1 rounded border-slate-300">
        <button class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Guardar</button>
    </form>

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-100">
                <tr>
                    <th class="px-4 py-3 text-left">ID</th>
                    <th class="px-4 py-3 text-left">Nombre</th>
                    <th class="px-4 py-3 text-left">Slug</th>
                    <th class="px-4 py-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody id="category-body"></tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script type="module">

const { apiFetch, showToast, validationErrors } = window.AppApi;
const body = document.getElementById('category-body');

async function loadCategories() {
    const categories = await apiFetch('/category');
    body.innerHTML = categories.map((c) => `
        <tr class="border-t">
            <td class="px-4 py-3">${c.id}</td>
            <td class="px-4 py-3">${c.name}</td>
            <td class="px-4 py-3 text-slate-500">${c.slug}</td>
            <td class="px-4 py-3 text-right space-x-2">
                <button data-edit="${c.id}" data-name="${c.name}" class="text-indigo-600">Editar</button>
                <button data-delete="${c.id}" class="text-red-600">Eliminar</button>
            </td>
        </tr>
    `).join('');
}

document.getElementById('category-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const name = document.getElementById('category-name').value;
    try {
        await apiFetch('/category', { method: 'POST', body: { name } });
        document.getElementById('category-name').value = '';
        showToast('Categoría guardada');
        await loadCategories();
    } catch (error) {
        const errors = validationErrors(error);
        showToast(errors.name?.[0] ?? window.AppApi.errorText(error), 'error');
    }
});

body.addEventListener('click', async (e) => {
    const deleteId = e.target.dataset.delete;
    const editId = e.target.dataset.edit;

    if (deleteId) {
        if (!confirm('¿Eliminar categoría?')) return;
        await apiFetch(`/category/${deleteId}`, { method: 'DELETE' });
        showToast('Categoría eliminada');
        await loadCategories();
    }

    if (editId) {
        const name = prompt('Nuevo nombre', e.target.dataset.name);
        if (!name) return;
        await apiFetch(`/category/${editId}`, { method: 'PUT', body: { name } });
        showToast('Categoría actualizada');
        await loadCategories();
    }
});

loadCategories();
</script>
