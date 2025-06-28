@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-gray-300 focus:border-maroon-500 focus:ring-maroon-500 rounded-lg shadow-sm']) !!}>
