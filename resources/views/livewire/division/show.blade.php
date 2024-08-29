<x-crud.header>Divisions</x-crud.header>

<div class="w-full">
    <x-crud.subheader back-route="divisions.index">Details of the {{ $division->name }} division</x-crud.subheader>
    <x-crud.content>
        <x-crud.show.table>
            <x-crud.show.model-field label="Season">{{ $division->competition->season->name }}</x-crud.show.model-field>
            <x-crud.show.model-field label="Competition">{{ $division->competition->name }}</x-crud.show.model-field>
            <x-crud.show.model-field label="Name">{{ $division->name }}</x-crud.show.model-field>
            <x-crud.show.model-field label="Display order">{{ $division->display_order }}</x-crud.show.model-field>
        </x-crud.show.table>
    </x-crud.content>
</div>
