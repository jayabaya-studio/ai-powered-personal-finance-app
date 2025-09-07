@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-white/20 bg-white/5 text-gray-200 focus:border-purple-500 focus:ring-purple-500 rounded-md shadow-sm placeholder-gray-500']) }}>
