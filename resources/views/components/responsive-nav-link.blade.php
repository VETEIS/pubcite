@props(['active'])

@php
$classes = ($active ?? false)
    ? 'block w-full ps-3 pe-4 py-2 pb-1 border-b-4 border-maroon-700 text-start text-base font-medium text-white hover:text-maroon-200 focus:outline-none transition duration-150 ease-in-out'
    : 'block w-full ps-3 pe-4 py-2 border-b-2 border-transparent text-start text-base font-medium text-white text-opacity-60 hover:text-maroon-200 focus:outline-none transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
