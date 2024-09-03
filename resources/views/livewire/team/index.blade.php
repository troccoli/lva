<x-crud.header>Teams</x-crud.header>

<div class="w-full">
    <x-crud.subheader add-route="teams.create" class="mb-4">A list of all the teams in the system</x-crud.subheader>
    <livewire:filters :filters="$filters"/>
    <x-crud.content>
        <x-crud.index.table columns="name,venue">
            @foreach ($teams as $team)
                <x-crud.index.row row-key="{{ $team->getKey() }}">
                    <x-crud.index.cell>{{ $team->name }}</x-crud.index.cell>
                    <x-crud.index.cell>{{ $team->venue->name }}</x-crud.index.cell>
                    <x-crud.index.cell class="flex gap-1 pl-2 pr-0 font-medium">
                        <x-crud.index.show-button route="teams.show" :model="$team" />
                        <x-crud.index.edit-button route="teams.edit" :model="$team" />
                        <x-crud.index.delete-button :model="$team">
                            Are you sure you want to delete team {{ $team->name }}?"
                        </x-crud.index.delete-button>
                    </x-crud.index.cell>
                </x-crud.index.row>
            @endforeach
        </x-crud.index.table>

        <div class="mt-4 px-4">
            {!! $teams->withQueryString()->links() !!}
        </div>
    </x-crud.content>
</div>
