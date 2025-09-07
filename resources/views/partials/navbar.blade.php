<!-- Top Navbar -->
<nav class="h-16 bg-black/40 backdrop-blur-xl border-b border-white/10 flex items-center justify-between px-4 sm:px-6 shrink-0 relative z-30">
    <!-- Hamburger Button (Mobile Only) -->
    <button @click="open = !open" class="lg:hidden text-gray-400 hover:text-white">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
    </button>

    <!-- Logo placeholder for mobile (optional) -->
    <div class="lg:hidden text-xl font-bold text-white mr-auto ml-4">FinancePFM</div>

    <!-- Right side of Navbar -->
    <div class="flex items-center gap-4 ml-auto lg:order-3">
        <!-- Notifications Dropdown -->
        <div x-data="notificationsManager" class="relative">
            <button @click="open = !open" class="relative text-gray-400 hover:text-white">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <template x-if="unreadCount > 0">
                    <span class="absolute -top-2 -right-2 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-xs text-white" x-text="unreadCount"></span>
                </template>
            </button>

            <!-- Dropdown Panel -->
            <div x-show="open" @click.outside="open = false" style="display: none;"
                 class="absolute right-0 mt-2 w-80 bg-gray-800/80 backdrop-blur-lg rounded-lg shadow-lg ring-1 ring-white/10 z-50">
                <div class="p-3 border-b border-white/10 flex justify-between items-center">
                    <h3 class="font-semibold text-white">Notifications</h3>
                </div>
                <div class="max-h-96 overflow-y-auto">
                    {{-- Loading State --}}
                    <template x-if="isLoading">
                        <p class="p-4 text-sm text-gray-400 text-center">Loading...</p>
                    </template>
                    {{-- Empty State --}}
                    <template x-if="!isLoading && notifications.length === 0">
                        <p class="p-4 text-sm text-gray-400 text-center">No new notifications.</p>
                    </template>
                    {{-- Notifications List --}}
                    <template x-for="notification in notifications" :key="notification.id">
                        <div class="p-3 text-sm text-gray-300 border-b border-white/5 hover:bg-white/5 flex gap-3">
                            <div class="flex-shrink-0 pt-1">
                                <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.22 1.754a.75.75 0 011.56 0l1.652 4.955a.75.75 0 00.723.545h5.204a.75.75 0 01.44 1.352l-4.223 3.08a.75.75 0 00-.278.83l1.653 4.955a.75.75 0 01-1.14.83l-4.223-3.08a.75.75 0 00-.884 0l-4.223 3.08a.75.75 0 01-1.14-.83l1.653-4.955a.75.75 0 00-.278-.83L.384 8.604a.75.75 0 01.44-1.352h5.204a.75.75 0 00.723-.545L8.22 1.754z" clip-rule="evenodd" /></svg>
                            </div>
                            <div class="flex-1">
                                <p x-text="notification.data.message"></p>
                                <button @click="markAsRead(notification.id)" class="text-xs text-blue-400 hover:underline mt-1">Mark as read</button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- User Profile Dropdown -->
        <div x-data="{ open: false }" class="relative">
            <button @click="open = ! open" class="flex items-center gap-2 text-gray-300 hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 rounded-lg py-1.5 px-3 transition-colors">
                <span class="font-semibold">{{ Auth::user()->name }}</span>
                <svg class="h-5 w-5 transform transition-transform duration-200" :class="{ 'rotate-180': open }" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>
            <div x-show="open" @click.outside="open = false" style="display: none;"
                 class="absolute right-0 mt-2 w-48 bg-gray-800/80 backdrop-blur-lg rounded-lg shadow-lg py-1 ring-1 ring-white/10 z-50">
                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-300 hover:bg-white/10 hover:text-white transition-colors">Profile</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a href="{{ route('logout') }}"
                       onclick="event.preventDefault(); this.closest('form').submit();"
                       class="block w-full text-left px-4 py-2 text-sm text-gray-300 hover:bg-white/10 hover:text-white transition-colors">
                        Log Out
                    </a>
                </form>
            </div>
        </div>
    </div>
</nav>
