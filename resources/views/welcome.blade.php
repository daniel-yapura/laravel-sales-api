@extends('layouts.app')

@section('title', 'Inicio')
@section('content')
<div class="text-center py-16">
    <h1 class="text-4xl font-bold mb-4">Sales Module</h1>
    <p class="text-slate-600 mb-6">Gestión de productos, inventario, ventas e impuestos con JWT.</p>
    <a href="/login" class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700">Iniciar sesión</a>
</div>
@endsection
