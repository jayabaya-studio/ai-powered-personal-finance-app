<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Three.js from CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>

    @vite(['resources/css/app.css'])

    <style>
        body {
            background-image: url('{{ asset('storage/background_id.jpg') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            overflow-x: hidden;
            position: relative;
        }
        .snow-particle {
            position: absolute;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 50%;
            pointer-events: none;
            animation: snowfall linear infinite;
        }
        @keyframes snowfall {
            0% { transform: translateY(-10vh) translateX(0); opacity: 0; }
            10% { opacity: 0.8; }
            100% { transform: translateY(100vh) translateX(50px); opacity: 0; }
        }
    </style>
</head>
<body class="font-sans antialiased text-gray-200">
    <div id="snow-container" class="fixed inset-0 z-0 pointer-events-none overflow-hidden"></div>

    <div x-data="{ open: false }" class="min-h-screen flex relative z-10">
        
        @include('partials.sidebar-user')

        <div class="flex-1 flex flex-col relative z-20">
            @include('partials.navbar')
            
            <main class="p-4 sm:p-6 lg:p-8 flex-1">
                @yield('content')
            </main>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // [PERBAIKAN PERFORMA]
            // Hanya jalankan efek salju di perangkat desktop (lebar > 768px)
            // untuk menghindari lag dan konsumsi baterai di mobile.
            if (window.innerWidth >= 768) {
                const snowContainer = document.getElementById('snow-container');
                if (!snowContainer) return;
                const numberOfSnowflakes = 50;
                for (let i = 0; i < numberOfSnowflakes; i++) {
                    const snowflake = document.createElement('div');
                    snowflake.classList.add('snow-particle');
                    const size = Math.random() * 5 + 2;
                    snowflake.style.width = `${size}px`;
                    snowflake.style.height = `${size}px`;
                    snowflake.style.left = `${Math.random() * 100}vw`;
                    const duration = Math.random() * 10 + 5;
                    snowflake.style.animationDuration = `${duration}s`;
                    const delay = Math.random() * -duration;
                    snowflake.style.animationDelay = `${delay}s`;
                    snowflake.style.opacity = Math.random() * 0.8 + 0.2;
                    snowContainer.appendChild(snowflake);
                }
            }
        });
    </script>
    @stack('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('notificationsManager', () => ({
                open: false,
                notifications: [],
                unreadCount: 0,
                isLoading: true,
                init() {
                    this.fetchNotifications();
                    setInterval(() => this.fetchNotifications(), 60000); // Refresh every minute
                },
                fetchNotifications() {
                    fetch('{{ route("notifications.index") }}')
                        .then(response => response.json())
                        .then(data => {
                            this.notifications = data.notifications;
                            this.unreadCount = data.unread_count;
                            this.isLoading = false;
                        }).catch(() => this.isLoading = false);
                },
                markAsRead(notificationId) {
                    fetch(`/notifications/${notificationId}/read`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                        },
                    }).then(response => {
                        if(response.ok) {
                            this.notifications = this.notifications.filter(n => n.id !== notificationId);
                            this.unreadCount--;
                        }
                    });
                }
            }));
        });
    </script>
    {{-- Floating AI Assistant --}}
    <x-ai-assistant />

    @vite(['resources/js/app.js'])
</body>
</html>
