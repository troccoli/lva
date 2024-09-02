<x-crud.header>Clubs</x-crud.header>

<div class="w-full">
    <x-crud.subheader back-route="clubs.index">Details of the {{ $club->name }} club</x-crud.subheader>
    <x-crud.content>
        <x-crud.show.table>
            <x-crud.show.model-field label="Name">{{ $club->name }}</x-crud.show.model-field>
            <x-crud.show.model-field label="Venue">{{ $club->venue->name }}</x-crud.show.model-field>
        </x-crud.show.table>
    </x-crud.content>
</div>
