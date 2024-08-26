@props([
'columns',
])

@php
$columns = explode(',', $columns)
@endphp
<table class="w-full divide-y divide-gray-100 dark:divide-gray-700">
    <thead>
    <tr>
        @foreach($columns as $column)
        <th scope="col"
            class="py-2 pr-3 text-left text-xs font-semibold uppercase tracking-wide">
            {{ $column }}
        </th>
        @endforeach

        <th scope="col"
            class="py-2 pl-3 text-left text-xs font-semibold uppercase tracking-wide">
            Actions
        </th>
    </tr>
    </thead>
    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
        {{ $slot }}
    </tbody>
</table>
