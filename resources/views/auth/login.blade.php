@extends('layouts.app')

@section('title', 'Login')
@section('content')
<div class="max-w-md mx-auto bg-white rounded-xl shadow p-6">
    <h1 class="text-2xl font-bold mb-4">Iniciar sesión</h1>
    <form id="login-form" class="space-y-4">
        <div>
            <label class="block text-sm font-medium">Email</label>
            <input id="email" type="email" required class="mt-1 w-full rounded border-slate-300">
        </div>
        <div>
            <label class="block text-sm font-medium">Contraseña</label>
            <input id="password" type="password" required class="mt-1 w-full rounded border-slate-300">
        </div>
        <button class="w-full bg-indigo-600 text-white py-2 rounded hover:bg-indigo-700">Entrar</button>
        <p class="text-sm text-slate-600">¿No tienes cuenta? <a href="/register" class="text-indigo-600">Regístrate</a></p>
    </form>
</div>
@endsection
