@props(['user', 'size' => 'md', 'class' => ''])
@php
    $name = trim($user->name ?? '') ?: 'User';
    $initial = $name !== '' ? mb_substr($name, 0, 1) : 'U';
    $initialUpper = function_exists('mb_strtoupper') ? mb_strtoupper($initial) : strtoupper($initial);

    $sizes = [
        'xs' => ['wrapper' => 'w-6 h-6',  'img' => 'w-6 h-6',  'text' => 'text-[10px]'],
        'sm' => ['wrapper' => 'w-8 h-8',  'img' => 'w-8 h-8',  'text' => 'text-xs'],
        'md' => ['wrapper' => 'w-10 h-10', 'img' => 'w-10 h-10', 'text' => 'text-sm'],
        'lg' => ['wrapper' => 'w-12 h-12', 'img' => 'w-12 h-12', 'text' => 'text-base'],
    ];
    $dim = $sizes[$size] ?? $sizes['md'];

    $palettes = [
        ['from-indigo-500', 'to-purple-600'],
        ['from-pink-500', 'to-rose-500'],
        ['from-amber-400', 'to-orange-500'],
        ['from-emerald-400', 'to-teal-500'],
        ['from-sky-400', 'to-blue-600'],
        ['from-violet-500', 'to-fuchsia-500'],
    ];
    $idx = ((int) ($user->id ?? 0)) % count($palettes);
    $grad = $palettes[$idx];
@endphp

@if(!empty($user->avatar))
    <img
        src="{{ \Illuminate\Support\Facades\Storage::url($user->avatar) }}"
        alt="{{ e($name) }}のアバター"
        loading="lazy"
        decoding="async"
        class="object-cover rounded-full ring-1 ring-gray-200 shadow-sm {{ $dim['img'] }} {{ $class }}"
        title="{{ e($name) }}"
    />
@else
    <div
        class="rounded-full flex items-center justify-center bg-gradient-to-br {{ $grad[0] }} {{ $grad[1] }} text-black font-semibold ring-1 ring-gray-200 shadow-sm {{ $dim['wrapper'] }} {{ $class }}"
        title="{{ e($name) }}"
        aria-label="{{ e($name) }}"
    >
        <span class="leading-none select-none {{ $dim['text'] }}">{{ $initialUpper }}</span>
    </div>
@endif

