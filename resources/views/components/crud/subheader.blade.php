@props(['back' => false, 'create' => false, 'createUrl' => '' ])

<div {{ $attributes->merge(['class' => 'mx-1 flex w-full items-center']) }}>
    <p class="text-md grow">{{ $slot }}</p>
    <div class="grow-0">
        @if ($back)
            <x-secondary-button onclick="javascript:history.back()">Back</x-secondary-button>
        @elseif ($create && $createUrl)
            <x-crud.index.create-button href="{{ $createUrl }}" />
        @endif
    </div>
</div>
