<x-crud.header>Fixtures</x-crud.header>

<div class="w-full">
    <x-crud.subheader add-route="fixtures.create" class="mb-4">
        A list of all the fixtures in the system
    </x-crud.subheader>
    <livewire:filters :filters="$filters"/>
    <x-crud.content>
        <x-crud.index.table columns="#,home team, away team, date, start time, venue">
            @foreach ($fixtures as $fixture)
                <x-crud.index.row row-key="{{ $fixture->getKey() }}">
                    <x-crud.index.cell>{{ $fixture->match_number }}</x-crud.index.cell>
                    <x-crud.index.cell>{{ $fixture->homeTeam->name }}</x-crud.index.cell>
                    <x-crud.index.cell>{{ $fixture->awayTeam->name }}</x-crud.index.cell>
                    <x-crud.index.cell>{{ $fixture->matchDatetime->toDateString() }}</x-crud.index.cell>
                    <x-crud.index.cell>{{ $fixture->matchDatetime->toTimeString() }}</x-crud.index.cell>
                    <x-crud.index.cell>{{ $fixture->venue->name }}</x-crud.index.cell>
                    <x-crud.index.cell class="flex gap-1 pl-2 pr-0 font-medium">
                        <x-crud.index.show-button route="fixtures.show" :model="$fixture" />
                        <x-crud.index.edit-button route="fixtures.edit" :model="$fixture" />
                        <x-crud.index.delete-button :model="$fixture">
                            Are you sure?
                        </x-crud.index.delete-button>
                    </x-crud.index.cell>
                </x-crud.index.row>
            @endforeach
        </x-crud.index.table>

        <div class="mt-4 px-4">
            {!! $fixtures->withQueryString()->links() !!}
        </div>
    </x-crud.content>
</div>
