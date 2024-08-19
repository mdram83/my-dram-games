@props(['isActive', 'isPremium'])

@php($colorClass = !$isActive ? 'bg-gray-500' : (!$isPremium ? 'bg-green-500' : 'bg-pink-600'))
@php($statusText = !$isActive ? 'Not Available' : (!$isPremium ? 'Available' : 'Premium'))

<span class="{{ $colorClass }} rounded-xl text-center text-white">
    {{ $statusText }}
</span>
