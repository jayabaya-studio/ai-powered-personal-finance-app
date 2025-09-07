@extends('layouts.admin')

@section('content')
    <!-- Header Halaman -->
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold text-white">Admin Dashboard</h1>
    </div>

    <!-- Grid untuk Widget Admin -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Contoh Widget Total Pengguna -->
        <x-card-glass>
            <h3 class="text-lg font-semibold text-gray-300">Total Users</h3>
            <p class="text-4xl font-bold text-white mt-2">1,250</p>
        </x-card-glass>

        <!-- Contoh Widget Transaksi Hari Ini -->
        <x-card-glass>
            <h3 class="text-lg font-semibold text-gray-300">Transactions Today</h3>
            <p class="text-4xl font-bold text-white mt-2">5,430</p>
        </x-card-glass>

        <!-- Contoh Widget Pendapatan (jika ada fitur premium) -->
        <x-card-glass>
            <h3 class="text-lg font-semibold text-gray-300">Revenue (This Month)</h3>
            <p class="text-4xl font-bold text-green-400 mt-2">$ 1,200</p>
        </x-card-glass>
    </div>

    <!-- Tabel atau daftar aktivitas terbaru bisa ditambahkan di sini -->
    <div class="mt-8">
        <x-card-glass>
            <h3 class="text-xl font-semibold text-white mb-4">Recent Activity</h3>
            <p class="text-gray-400">Tabel aktivitas pengguna akan ditampilkan di sini.</p>
        </x-card-glass>
    </div>
@endsection
