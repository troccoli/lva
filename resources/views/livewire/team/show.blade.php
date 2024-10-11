<div class="w-full">
    <x-crud.header>Teams</x-crud.header>
    <x-crud.subheader back>
        Details of the {{ $team->name }} team
    </x-crud.subheader>

    <x-crud.content>
        <x-crud.show.table>
            <x-crud.show.model-field label="Club">{{ $team->club->name }}</x-crud.show.model-field>
            <x-crud.show.model-field label="Name">{{ $team->name }}</x-crud.show.model-field>
        </x-crud.show.table>
    </x-crud.content>
</div>
