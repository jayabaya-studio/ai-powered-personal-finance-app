{{--
    Sidebar for Users (Upgraded & Troubleshooted Version)
    - Fixes mobile functionality (auto-close on link click).
    - Implements a futuristic design with active indicators and hover effects.
    - Groups menu items for better organization.

    IMPORTANT: This component requires Alpine.js to be loaded and initialized in your main layout file (e.g., app.blade.php).
--}}
<aside x-data="{ open: false }"
       @keydown.window.escape="open = false"
       @open-sidebar.window="open = true"
       x-cloak
       class="fixed inset-y-0 left-0 z-50 w-64 h-screen bg-gray-900/50 backdrop-blur-xl border-r border-white/10 flex flex-col
              transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out
              lg:static lg:inset-auto"
       :class="{ 'translate-x-0': open, '-translate-x-full': !open }">

    <!-- Overlay for mobile, click to close sidebar -->
    <div x-show="open" @click="open = false" class="fixed inset-0 z-40 bg-black/60 lg:hidden" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>

    <!-- Logo & Close Button (Mobile Only) -->
    <div class="h-16 flex items-center justify-between px-6 border-b border-white/10 lg:justify-center shrink-0">
        <a href="{{ route('dashboard') }}" class="text-2xl font-bold text-white tracking-wider">
            FinancePFM
        </a>
        <button @click="open = false" class="lg:hidden text-gray-400 hover:text-white">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <!-- Navigation Menu -->
    <nav class="flex-1 p-4 space-y-4 overflow-y-auto">
        {{-- Main Menu --}}
        <a href="{{ route('dashboard') }}" @click="open = false"
           class="relative flex items-center gap-3 px-4 py-2.5 transition-all duration-200 group
                  {{ request()->routeIs('dashboard') ? 'text-white' : 'text-gray-400 hover:text-white' }}">
            <span class="absolute left-0 h-full w-1 rounded-r-full bg-purple-400 transition-all duration-200
                         {{ request()->routeIs('dashboard') ? 'opacity-100' : 'opacity-0 group-hover:opacity-50' }}"></span>
            <div class="transform transition-transform duration-200 group-hover:translate-x-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" /></svg>
            </div>
            <span class="transform transition-transform duration-200 group-hover:translate-x-1">Dashboard</span>
        </a>

        {{-- Activity Section --}}
        <div>
            <h3 class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Activity</h3>
            <div class="mt-2 space-y-1">
                <a href="{{ route('transactions.index') }}" @click="open = false"
                   class="relative flex items-center gap-3 px-4 py-2.5 transition-all duration-200 group
                          {{ request()->routeIs('transactions.*') ? 'text-white' : 'text-gray-400 hover:text-white' }}">
                    <span class="absolute left-0 h-full w-1 rounded-r-full bg-purple-400 transition-all duration-200
                                 {{ request()->routeIs('transactions.*') ? 'opacity-100' : 'opacity-0 group-hover:opacity-50' }}"></span>
                    <div class="transform transition-transform duration-200 group-hover:translate-x-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M2 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1H3a1 1 0 01-1-1V4zm8 0a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4zM16 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z" /></svg>
                    </div>
                    <span class="transform transition-transform duration-200 group-hover:translate-x-1">Transactions</span>
                </a>
                <a href="{{ route('recurring-transactions.index') }}" @click="open = false"
                   class="relative flex items-center gap-3 px-4 py-2.5 transition-all duration-200 group
                          {{ request()->routeIs('recurring-transactions.*') ? 'text-white' : 'text-gray-400 hover:text-white' }}">
                    <span class="absolute left-0 h-full w-1 rounded-r-full bg-purple-400 transition-all duration-200
                                 {{ request()->routeIs('recurring-transactions.*') ? 'opacity-100' : 'opacity-0 group-hover:opacity-50' }}"></span>
                    <div class="transform transition-transform duration-200 group-hover:translate-x-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h5M4 12a8 8 0 018-8v0a8 8 0 018 8v0a8 8 0 01-8 8v0a8 8 0 01-8-8v0z" /></svg>
                    </div>
                    <span class="transform transition-transform duration-200 group-hover:translate-x-1">Recurring</span>
                </a>
            </div>
        </div>

        {{-- Planning Section --}}
        <div>
            <h3 class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Planning</h3>
            <div class="mt-2 space-y-1">
                <a href="{{ route('budgets.index') }}" @click="open = false"
                   class="relative flex items-center gap-3 px-4 py-2.5 transition-all duration-200 group
                          {{ request()->routeIs('budgets.*') ? 'text-white' : 'text-gray-400 hover:text-white' }}">
                    <span class="absolute left-0 h-full w-1 rounded-r-full bg-purple-400 transition-all duration-200
                                 {{ request()->routeIs('budgets.*') ? 'opacity-100' : 'opacity-0 group-hover:opacity-50' }}"></span>
                    <div class="transform transition-transform duration-200 group-hover:translate-x-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                    </div>
                    <span class="transform transition-transform duration-200 group-hover:translate-x-1">Budgets</span>
                </a>
                <a href="{{ route('goals.index') }}" @click="open = false"
                   class="relative flex items-center gap-3 px-4 py-2.5 transition-all duration-200 group
                          {{ request()->routeIs('goals.*') ? 'text-white' : 'text-gray-400 hover:text-white' }}">
                    <span class="absolute left-0 h-full w-1 rounded-r-full bg-purple-400 transition-all duration-200
                                 {{ request()->routeIs('goals.*') ? 'opacity-100' : 'opacity-0 group-hover:opacity-50' }}"></span>
                    <div class="transform transition-transform duration-200 group-hover:translate-x-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21H3.737a2 2 0 01-1.789-2.894l3.5-7A2 2 0 017.237 9H14zm0 0V5a2 2 0 012-2h2a2 2 0 012 2v5m-6 0h-2.5m-4.5 0H7M21 12h-1M7 12H6m-2 0H3" /></svg>
                    </div>
                    <span class="transform transition-transform duration-200 group-hover:translate-x-1">Financial Goals</span>
                </a>
            </div>
        </div>

        {{-- Management Section --}}
        <div>
            <h3 class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Management</h3>
            <div class="mt-2 space-y-1">
                <a href="{{ route('accounts.index') }}" @click="open = false"
                   class="relative flex items-center gap-3 px-4 py-2.5 transition-all duration-200 group
                          {{ request()->routeIs('accounts.*') ? 'text-white' : 'text-gray-400 hover:text-white' }}">
                    <span class="absolute left-0 h-full w-1 rounded-r-full bg-purple-400 transition-all duration-200
                                 {{ request()->routeIs('accounts.*') ? 'opacity-100' : 'opacity-0 group-hover:opacity-50' }}"></span>
                    <div class="transform transition-transform duration-200 group-hover:translate-x-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z" /><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.022 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" /></svg>
                    </div>
                    <span class="transform transition-transform duration-200 group-hover:translate-x-1">Accounts</span>
                </a>
                <a href="{{ route('cards.index') }}" @click="open = false"
                   class="relative flex items-center gap-3 px-4 py-2.5 transition-all duration-200 group
                          {{ request()->routeIs('cards.*') ? 'text-white' : 'text-gray-400 hover:text-white' }}">
                    <span class="absolute left-0 h-full w-1 rounded-r-full bg-purple-400 transition-all duration-200
                                 {{ request()->routeIs('cards.*') ? 'opacity-100' : 'opacity-0 group-hover:opacity-50' }}"></span>
                    <div class="transform transition-transform duration-200 group-hover:translate-x-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>
                    </div>
                    <span class="transform transition-transform duration-200 group-hover:translate-x-1">My Cards</span>
                </a>
                <a href="{{ route('categories.index') }}" @click="open = false"
                   class="relative flex items-center gap-3 px-4 py-2.5 transition-all duration-200 group
                          {{ request()->routeIs('categories.*') ? 'text-white' : 'text-gray-400 hover:text-white' }}">
                    <span class="absolute left-0 h-full w-1 rounded-r-full bg-purple-400 transition-all duration-200
                                 {{ request()->routeIs('categories.*') ? 'opacity-100' : 'opacity-0 group-hover:opacity-50' }}"></span>
                    <div class="transform transition-transform duration-200 group-hover:translate-x-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h12v4a2 2 0 002-2V6a2 2 0 00-2-2H4zm0 6a2 2 0 00-2 2v4a2 2 0 002 2h12a2 2 0 002-2v-4a2 2 0 00-2-2H4z" clip-rule="evenodd" /></svg>
                    </div>
                    <span class="transform transition-transform duration-200 group-hover:translate-x-1">Categories</span>
                </a>
            </div>
        </div>

        {{-- Premium/Collaboration Section --}}
        <div>
            <h3 class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Collaboration</h3>
            <div class="mt-2 space-y-1">
                <a href="{{ route('families.index') }}" @click="open = false"
                   class="relative flex items-center gap-3 px-4 py-2.5 transition-all duration-200 group
                          {{ request()->routeIs('families.*') ? 'text-white' : 'text-gray-400 hover:text-white' }}">
                    <span class="absolute left-0 h-full w-1 rounded-r-full bg-purple-400 transition-all duration-200
                                 {{ request()->routeIs('families.*') ? 'opacity-100' : 'opacity-0 group-hover:opacity-50' }}"></span>
                    <div class="transform transition-transform duration-200 group-hover:translate-x-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h2a2 2 0 002-2V7a2 2 0 00-2-2h-2m-4 12V5m0 12h-3a2 2 0 01-2-2v-2m4 2V5m0 12h3a2 2 0 002-2v-2m-7 2H4a2 2 0 01-2-2V7a2 2 0 012-2h2" /></svg>
                    </div>
                    <span class="transform transition-transform duration-200 group-hover:translate-x-1">Family Spaces</span>
                </a>
            </div>
        </div>

    </nav>
</aside>
