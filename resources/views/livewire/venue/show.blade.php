<x-crud.header>Venues</x-crud.header>

<div class="w-full">
    <x-crud.subheader back-route="venues.index">Details of the {{ $venue->name }} venue</x-crud.subheader>
    <x-crud.content>
        <x-crud.show.table>
            <x-crud.show.model-field label="Name">{{ $venue->name }}</x-crud.show.model-field>
        </x-crud.show.table>
    </x-crud.content>
</div>
