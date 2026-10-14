<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Sales Module')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-slate-50 text-slate-900">
    <header class="bg-white border-b border-slate-200">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
            <a href="/" class="font-bold text-lg text-indigo-600">Sales Module</a>
            <div class="flex items-center gap-4 text-sm" id="nav-links">
                <a href="/products" class="hover:text-indigo-600">Productos</a>
                <a href="/categories" class="hover:text-indigo-600">Categorías</a>
                <a href="/sales" class="hover:text-indigo-600">Ventas</a>
                <a href="/tax" class="hover:text-indigo-600">IVA</a>
                <a href="/login" class="hover:text-indigo-600" id="login-link">Login</a>
                <button id="logout-btn" class="hidden text-red-600 hover:text-red-700">Logout</button>
            </div>
        </nav>
    </header>
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @yield('content')
    </main>
    <div id="toast" class="fixed bottom-4 right-4 hidden px-4 py-2 rounded-lg text-white shadow-lg"></div>
    @stack('scripts')
</body>
</html>
