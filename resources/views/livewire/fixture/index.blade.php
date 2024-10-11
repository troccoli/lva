<div class="w-full">
    <x-crud.header>Fixtures</x-crud.header>
    <x-crud.subheader create create-url="{{ $createUrl }}">
        A list of all the fixtures in the system
    </x-crud.subheader>

    <x-filters-section>
        <livewire:seasons.filter />
        <livewire:competitions.filter />
        <livewire:divisions.filter />
    </x-filters-section>

    <x-crud.content>
        <x-crud.index.table class="md:hidden" columns="">
            @foreach ($fixtures as $fixture)
                <x-crud.index.row row-key="{{ $fixture->getKey() }}">
                    <x-crud.index.cell>
                        <span class="font-bold">Match # {{ $fixture->match_number }}</span>
                        <br />
                        {{ $fixture->homeTeam->name }} v {{ $fixture->awayTeam->name }}
                        <br />
                        on {{ $fixture->matchDateTime->isoFormat('LLLL') }}
                        <br />
                        at {{ $fixture->venue->name }}
                        <br />
                        <div class="flex gap-1 mt-2 pr-0 font-medium">
                            <x-crud.index.show-button route="fixtures.show" :model="$fixture" />
                            <x-crud.index.edit-button route="fixtures.edit" :model="$fixture" />
                            <x-crud.index.delete-button :model="$fixture">
                                Are you sure?
                            </x-crud.index.delete-button>
                        </div>
                    </x-crud.index.cell>
                </x-crud.index.row>
            @endforeach
        </x-crud.index.table>
        <x-crud.index.table class="hidden md:block" columns="#,home team,away team,date,start time,venue,actions">
            @foreach ($fixtures as $fixture)
                <x-crud.index.row row-key="{{ $fixture->getKey() }}">
                    <x-crud.index.cell>{{ $fixture->match_number }}</x-crud.index.cell>
                    <x-crud.index.cell>{{ $fixture->homeTeam->name }}</x-crud.index.cell>
                    <x-crud.index.cell>{{ $fixture->awayTeam->name }}</x-crud.index.cell>
                    <x-crud.index.cell>{{ $fixture->matchDatetime->toDateString() }}</x-crud.index.cell>
                    <x-crud.index.cell>{{ $fixture->matchDatetime->toTimeString() }}</x-crud.index.cell>
                    <x-crud.index.cell>{{ $fixture->venue->name }}</x-crud.index.cell>
                    <x-crud.index.cell class="flex gap-1 pr-0 font-medium">
                        <x-crud.index.show-button route="fixtures.show" :model="$fixture" />
                        <x-crud.index.edit-button route="fixtures.edit" :model="$fixture" />
                        <x-crud.index.delete-button :model="$fixture">
                            Are you sure?
                        </x-crud.index.delete-button>
                    </x-crud.index.cell>
                </x-crud.index.row>
            @endforeach
        </x-crud.index.table>

        <div class="mt-4">
            {!! $fixtures->withQueryString()->links() !!}
        </div>
    </x-crud.content>
</div>
