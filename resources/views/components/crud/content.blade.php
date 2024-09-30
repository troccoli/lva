<div {{ $attributes->merge(['class' => 'px-1 overflow-x-auto']) }}>
    <div class="inline-block min-w-full align-middle">
        {{ $slot }}
    </div>
</div>
