@props(['title' => ''])

@php
    // This logic extracts the Alpine.js variable name from the 'show' or 'x-show' attribute.
    $showVariable = $attributes->get('show') ?? $attributes->get('x-show') ?? 'false';
@endphp

<div
    x-show="{{ $showVariable }}"
    x-on:keydown.escape.window="{{ $showVariable }} = false"
    style="display: none;"
    class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6"
    x-cloak
>
    <!-- Overlay Background -->
    <div @click="{{ $showVariable }} = false" x-show="{{ $showVariable }}" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black/70 backdrop-blur-sm"></div>

    <!-- Modal Content -->
    <div x-show="{{ $showVariable }}" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90" class="relative w-full max-w-lg mx-auto bg-gray-800/50 backdrop-blur-xl border border-white/20 rounded-2xl shadow-lg">
        <!-- Modal Header -->
        @if ($title)
        <div class="flex items-center justify-between p-4 border-b border-white/10">
            <h3 class="text-xl font-semibold text-white">{{ $title }}</h3>
            <button @click="{{ $showVariable }} = false" class="text-gray-400 hover:text-white transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
        @endif

        <!-- Modal Body -->
        <div class="text-gray-300 overflow-y-auto max-h-[80vh]">
            {{ $slot }}
        </div>
    </div>
</div>
