@extends('layouts.app')

@section('title', 'IVA')
@section('content')
<div class="max-w-xl mx-auto bg-white rounded-xl shadow p-6 space-y-4">
    <h1 class="text-2xl font-bold">Cálculo de IVA</h1>
    <div class="flex gap-3">
        <input id="tax-product-id" placeholder="ID de producto" type="number" class="flex-1 rounded border-slate-300">
        <button id="tax-calc" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Calcular</button>
    </div>
    <div id="tax-result" class="text-sm"></div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('tax-calc').addEventListener('click', async () => {
    const id = document.getElementById('tax-product-id').value;
    try {
        const data = await window.AppApi.apiFetch(`/info/tax/${id}`);
        document.getElementById('tax-result').innerHTML = `
            <p>Precio: <strong>${data.price}</strong></p>
            <p>Precio con IVA: <strong>${data.priceWithIVA}</strong></p>
        `;
    } catch (error) {
        window.AppApi.showToast(window.AppApi.errorText(error), 'error');
    }
});
</script>
