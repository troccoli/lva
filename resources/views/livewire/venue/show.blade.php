<div class="w-full">
    <x-crud.header>Venues</x-crud.header>
    <x-crud.subheader back>
        Details of the {{ $venue->name }} venue
    </x-crud.subheader>

    <x-crud.content>
        <x-crud.show.table>
            <x-crud.show.model-field label="Name">{{ $venue->name }}</x-crud.show.model-field>
        </x-crud.show.table>
    </x-crud.content>
</div>
