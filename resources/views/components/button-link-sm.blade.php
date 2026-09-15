@props(['href'])

<a {{ $attributes->merge(['class' => 'p-2 rounded-full hover:bg-gray-100 transition-colors duration-200']) }} href="{{ $href }}">
    {{ $slot }}
</a>
