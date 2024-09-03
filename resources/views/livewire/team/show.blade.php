<x-crud.header>Teams</x-crud.header>

<div class="w-full">
    <x-crud.subheader back-route="teams.index">Details of the {{ $team->name }} team</x-crud.subheader>
    <x-crud.content>
        <x-crud.show.table>
            <x-crud.show.model-field label="Club">{{ $team->club->name }}</x-crud.show.model-field>
            <x-crud.show.model-field label="Name">{{ $team->name }}</x-crud.show.model-field>
        </x-crud.show.table>
    </x-crud.content>
</div>
