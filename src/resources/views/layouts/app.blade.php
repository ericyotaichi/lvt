<!doctype html>
<html lang="zh-Hant">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? '村源科技' }}</title>
    @vite('resources/js/app.js')
    @inertiaHead
    
</head>

<body class="min-h-screen bg-gray-50 text-gray-900">
    <header class="bg-white/90 backdrop-blur border-b">
        <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
            <a href="/" class="flex items-center gap-2">
                <span class="inline-block w-8 h-8 rounded-full bg-gradient-to-br from-green-500 to-blue-600"></span>
                <span class="font-semibold">村源科技</span>
            </a>
            <nav class="hidden md:flex items-center gap-6 text-sm">
                <a href="/tech" class="hover:text-green-700">核心技術</a>
                <a href="/products" class="hover:text-green-700">產品與服務</a>
                <a href="/applications" class="hover:text-green-700">應用場域</a>
                <a href="/cases" class="hover:text-green-700">案例說明</a>
                <a href="/co-creation" class="hover:text-green-700">共創機遇</a>
                <a href="/about" class="hover:text-green-700">關於我們</a>
                <a href="/lead" class="px-3 py-1.5 rounded-xl bg-green-600 text-white hover:bg-green-700">聯繫方式</a>
            </nav>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4">
        @yield('content')
    </main>

    <footer class="mt-16 border-t">
        <div class="max-w-7xl mx-auto px-4 py-8 text-sm text-gray-500">
            © {{ date('Y') }} 村源科技
        </div>
    </footer>
</body>

</html>
