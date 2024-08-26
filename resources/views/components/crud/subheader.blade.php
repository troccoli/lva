@props(['addRoute' => '', 'backRoute' => ''])

<div {{ $attributes->merge(['class' => 'flex w-full items-center']) }}>
    <p class="text-md grow">{{ $slot }}</p>
    <div class="grow-0">
        @empty($addRoute)
            <x-secondary-button wire:navigate href="{{ route($backRoute) }}">Back</x-secondary-button>
        @else
            <x-crud.index.create-button route="{{ $addRoute }}" />
        @endif
    </div>
</div>
