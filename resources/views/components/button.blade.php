<button {{ $attributes->class([
    'inline-flex items-center px-6 py-2.5 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest transition ease-in-out duration-150',
    'bg-gradient-to-br from-blue-500 to-purple-600',
    'hover:from-blue-600 hover:to-purple-700',
    'focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500',
    'shadow-lg shadow-blue-500/30 hover:shadow-blue-500/60',
    'active:scale-95'
])->merge(['type' => 'submit']) }}>
    {{ $slot }}
</button>