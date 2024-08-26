<div {{ $attributes->merge(['class' => 'overflow-x-auto']) }}>
    <div class="inline-block min-w-full align-middle">
        {{ $slot }}
    </div>
</div>
