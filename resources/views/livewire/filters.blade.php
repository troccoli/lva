<?php
new class extends \Livewire\Volt\Component
{
    public array $filters;
}
?>

<div class = 'my-8 flex flex-col space-y-4 md:flex-row md:space-x-4 md:space-y-0'>
    @foreach( $filters as $key => $filter )
        <livewire:select-filter
            :wire:model="'filters.'.$key.'.currentOption'"
            :key="$key.now()"
            :label="$filter['label']"
            :options="$filter['options']"
            :selectedOption="$filter['currentOption']"
            :eventToEmit="$filter['event']"
        />
    @endforeach
</div>
