@props(['rowKey'])

<tr class="hover:bg-gray-50 dark:hover:bg-gray-700" wire:key="{{ $rowKey }}">
    {{ $slot }}
</tr>
