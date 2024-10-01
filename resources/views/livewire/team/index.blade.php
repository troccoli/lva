<div class="w-full">
    <x-crud.header>Teams</x-crud.header>
    <x-crud.subheader create create-url="{{ $createUrl }}">
        A list of all the teams in the system
    </x-crud.subheader>

    <x-filters-section>
        <livewire:clubs.filter />
    </x-filters-section>

    <x-crud.content>
        <x-crud.index.table class="md:hidden" columns="">
            @foreach ($teams as $team)
                <x-crud.index.row row-key="{{ $team->getKey() }}">
                    <x-crud.index.cell>
                        {{ $team->name }}
                        <br />
                        at {{ $team->venue->name }}
                        <br />
                        <div class="flex gap-1 mt-2 pr-0 font-medium">
                            <x-crud.index.show-button route="teams.show" :model="$team" />
                            <x-crud.index.edit-button route="teams.edit" :model="$team" />
                            <x-crud.index.delete-button :model="$team">
                                Are you sure you want to delete team {{ $team->name }}?"
                            </x-crud.index.delete-button>
                        </div>
                    </x-crud.index.cell>
                </x-crud.index.row>
            @endforeach
        </x-crud.index.table>
        <x-crud.index.table class="hidden md:block" columns="name,venue,actions">
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

        <div class="mt-4">
            {!! $teams->withQueryString()->links() !!}
        </div>
    </x-crud.content>
</div>
