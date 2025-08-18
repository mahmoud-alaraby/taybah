@props(['percent', 'color', 'text'])

@php
    $radius = 18; // نصف القطر
    $circumference = 2 * 3.1416 * $radius;
    $dash = $circumference * $percent / 100;
    $offset = $circumference - $dash;
@endphp

<div class="relative w-20 h-20 flex items-center justify-center select-none">
    <svg viewBox="0 0 40 40" width="80" height="80" class="transform -rotate-90">
        <circle
            cx="20" cy="20" r="{{ $radius }}"
            fill="none" stroke="#f3f4f6"
            stroke-width="4"
        />
        <circle
            cx="20" cy="20" r="{{ $radius }}"
            fill="none" stroke="{{ $color }}"
            stroke-width="4"
            stroke-dasharray="{{ $circumference }}"
            stroke-dashoffset="{{ $offset }}"
            style="transition: stroke-dashoffset 0.9s,cubic-bezier(.4,1.7,.6,.97);"
        />
    </svg>
    <span class="absolute top-1/2 left-1/2 text-lg font-bold"
        style="transform: translate(-50%, -50%); color:{{ $color }}">
        {{ $text }}
    </span>
</div>
