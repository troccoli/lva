<div class="w-full">
    <x-crud.header>Seasons</x-crud.header>
    <x-crud.subheader create create-url="{{ $createUrl }}">
        A list of all the seasons in the system
    </x-crud.subheader>

    <x-crud.content class="mt-4">
        <x-crud.index.table class="md:hidden" columns="">
            @foreach ($seasons as $season)
                <x-crud.index.row row-key="{{ $season->getKey() }}">
                    <x-crud.index.cell>
                        {{ $season->name }}
                        <br />
                        <div class="flex gap-1 mt-2 pr-0 font-medium">
                            <x-crud.index.show-button route="seasons.show" :model="$season" />
                            <x-crud.index.edit-button route="seasons.edit" :model="$season" />
                            <x-crud.index.delete-button :model="$season">
                                Are you sure you want to delete season {{ $season->name }}?"
                            </x-crud.index.delete-button>
                        </div>
                    </x-crud.index.cell>
                </x-crud.index.row>
            @endforeach
        </x-crud.index.table>
        <x-crud.index.table class="hidden md:block" columns="name,actions">
            @foreach ($seasons as $season)
                <x-crud.index.row row-key="{{ $season->getKey() }}">
                    <x-crud.index.cell>{{ $season->name }}</x-crud.index.cell>
                    <x-crud.index.cell class="flex gap-1 pl-2 pr-0 font-medium">
                        <x-crud.index.show-button route="seasons.show" :model="$season" />
                        <x-crud.index.edit-button route="seasons.edit" :model="$season" />
                        <x-crud.index.delete-button :model="$season">
                            Are you sure you want to delete season {{ $season->name }}?"
                        </x-crud.index.delete-button>
                    </x-crud.index.cell>
                </x-crud.index.row>
            @endforeach
        </x-crud.index.table>

        <div class="mt-4">
            {!! $seasons->withQueryString()->links() !!}
        </div>
    </x-crud.content>
</div>
