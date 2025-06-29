@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-gray-300 focus:border-maroon-700 focus:ring-maroon-700 rounded-lg shadow-sm']) !!}>
