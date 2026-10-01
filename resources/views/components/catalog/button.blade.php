@props(['href' => null, 'variant' => 'secondary'])
@php
    $variants = [
        'primary' => 'catalog-button-primary',
        'secondary' => 'catalog-button-secondary',
        'danger' => 'catalog-button-danger',
    ];
    $classes = 'catalog-button '.($variants[$variant] ?? $variants['secondary']);
@endphp
@if($href)
    <a href="{{ $href }}" {{ $attributes->class([$classes]) }}>{{ $slot }}</a>
@else
    <button {{ $attributes->merge(['type' => 'button'])->class([$classes]) }}>{{ $slot }}</button>
@endif
