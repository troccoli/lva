<div {{ $attributes->merge(['class' => 'mx-1 flex w-full items-center overflow-x-auto']) }}>
    <div class="inline-block min-w-full align-middle">
        {{ $slot }}
    </div>
</div>
