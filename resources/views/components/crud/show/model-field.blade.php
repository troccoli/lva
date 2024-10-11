@props(['label' => 'field'])

<div class="table-row">
    <div class="table-cell pl-1 pr-4 py-2 ">
        <p class="text-left text-xs font-semibold uppercase tracking-wide">{{ $label }}</p>
        <p class="md:hidden mt-1 md:mt-0 text-sm leading-6">{{ $slot }}</p>
    </div>
    <div class="hidden md:table-cell text-sm leading-6">{{ $slot }}</div>
</div>
