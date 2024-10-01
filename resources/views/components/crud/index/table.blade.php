@props(['columns'])

<table {{ $attributes->merge(['class' => 'w-full divide-y divide-gray-100 dark:divide-gray-700']) }}>
    <thead>
    <tr>
        @foreach(explode(',', $columns) as $column)
        <th scope="col"
            class="py-2 pr-3 text-left text-xs font-semibold uppercase tracking-wide">
            {{ $column }}
        </th>
        @endforeach
    </tr>
    </thead>
    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
        {{ $slot }}
    </tbody>
</table>
