<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Admin Panel - {{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts (Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-800 text-gray-200">
    <div class="min-h-screen">
        <div class="flex">
            <!-- Memanggil Sidebar untuk Admin -->
            @include('partials.sidebar-admin')

            <!-- Konten Utama -->
            <main class="flex-1">
                <!-- Memanggil Navbar Atas -->
                @include('partials.navbar')

                <!-- Konten Halaman -->
                <div class="p-8">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>
</body>
</html>
