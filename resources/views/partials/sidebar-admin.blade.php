<aside class="w-64 h-screen bg-gray-900/50 backdrop-blur-xl border-r border-white/10 flex flex-col">
    <!-- Logo -->
    <div class="h-16 flex items-center px-6 border-b border-white/10">
        <a href="{{ route('admin.dashboard') }}" class="text-2xl font-bold text-white">
            Admin Panel
        </a>
    </div>

    <!-- Menu Navigasi -->
    <nav class="flex-1 p-4 space-y-2">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg text-white hover:bg-white/10 transition-colors">
            <!-- Icon SVG Placeholder -->
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" /></svg>
            <span>Dashboard</span>
        </a>
        <a href="#" class="flex items-center gap-3 px-4 py-2 rounded-lg text-gray-400 hover:bg-white/10 hover:text-white transition-colors">
             <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" /></svg>
            <span>Users Management</span>
        </a>
    </nav>
</aside>
