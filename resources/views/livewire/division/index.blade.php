<div class="w-full">
    <x-crud.header>Divisions</x-crud.header>
    <x-crud.subheader create create-url="{{ $createUrl }}">
        A list of all the divisions in the system
    </x-crud.subheader>

    <x-filters-section>
        <livewire:seasons.filter />
        <livewire:competitions.filter />
    </x-filters-section>

    <x-crud.content>
        <x-crud.index.table class="md:hidden" columns="">
            @foreach ($divisions as $division)
                <x-crud.index.row row-key="{{ $division->getKey() }}">
                    <x-crud.index.cell>
                        {{ $division->name }}
                        <br />
                        <div class="flex gap-1 mt-2 pr-0 font-medium">
                            <x-crud.index.show-button route="divisions.show" :model="$division" />
                            <x-crud.index.edit-button route="divisions.edit" :model="$division" />
                            <x-crud.index.delete-button :model="$division">
                                Are you sure you want to delete the {{ $division->name }} divisions in
                                the {{ $division->competition->name }}
                                competition in the {{ $division->competition->season->name }} season?
                            </x-crud.index.delete-button>
                        </div>
                    </x-crud.index.cell>
                </x-crud.index.row>
            @endforeach
        </x-crud.index.table>
        <x-crud.index.table class="hidden md:block" columns="name,actions">
            @foreach ($divisions as $division)
                <x-crud.index.row row-key="{{ $division->getKey() }}">
                    <x-crud.index.cell>{{ $division->name }}</x-crud.index.cell>
                    <x-crud.index.cell class="flex gap-1 pl-2 pr-0 font-medium">
                        <x-crud.index.show-button route="divisions.show" :model="$division" />
                        <x-crud.index.edit-button route="divisions.edit" :model="$division" />
                        <x-crud.index.delete-button :model="$division">
                            Are you sure you want to delete the {{ $division->name }} divisions in
                            the {{ $division->competition->name }}
                            competition in the {{ $division->competition->season->name }} season?
                        </x-crud.index.delete-button>
                    </x-crud.index.cell>
                </x-crud.index.row>
            @endforeach
        </x-crud.index.table>

        <div class="mt-4">
            {!! $divisions->withQueryString()->links() !!}
        </div>
    </x-crud.content>
</div>
