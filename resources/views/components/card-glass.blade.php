{{-- 
    Ini adalah komponen kartu dengan efek glassmorphism yang disempurnakan.
    Memiliki border gradien halus untuk nuansa futuristik.
--}}

<div {{ $attributes->merge(['class' => 'relative rounded-2xl p-6 bg-white/5 backdrop-blur-md shadow-lg overflow-hidden']) }}> {{-- Adjusted background and blur for stronger glass effect --}}
    <!-- Efek Border Gradien -->
    <div class="absolute inset-0 rounded-2xl border border-transparent bg-gradient-to-br from-blue-400/30 to-purple-500/30 bg-origin-border opacity-50 pointer-events-none z-0"></div> {{-- Increased opacity for border gradient --}}
    
    <!-- Konten Slot -->
    <div class="relative z-10">
        {{ $slot }}
    </div>
</div>
