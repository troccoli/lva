<div class="w-full">
    <x-crud.header>Venues</x-crud.header>
    <x-crud.subheader create create-url="{{ $createUrl }}">
        A list of all the venues in the system
    </x-crud.subheader>

    <x-crud.content class="mt-4">
        <x-crud.index.table class="md:hidden" columns="">
            @foreach ($venues as $venue)
                <x-crud.index.row row-key="{{ $venue->getKey() }}">
                    <x-crud.index.cell>
                        {{ $venue->name }}
                        <br/>
                        <div class="flex gap-1 mt-2 pr-0 font-medium">
                            <x-crud.index.show-button route="venues.show" :model="$venue" />
                            <x-crud.index.edit-button route="venues.edit" :model="$venue" />
                            <x-crud.index.delete-button :model="$venue">
                                Are you sure you want to delete venue {{ $venue->name }}?"
                            </x-crud.index.delete-button>
                        </div>
                    </x-crud.index.cell>
                </x-crud.index.row>
            @endforeach
        </x-crud.index.table>
        <x-crud.index.table class="hidden md:block" columns="name,actions">
            @foreach ($venues as $venue)
                <x-crud.index.row row-key="{{ $venue->getKey() }}">
                    <x-crud.index.cell>{{ $venue->name }}</x-crud.index.cell>
                    <x-crud.index.cell class="flex gap-1 pl-2 pr-0 font-medium">
                        <x-crud.index.show-button route="venues.show" :model="$venue" />
                        <x-crud.index.edit-button route="venues.edit" :model="$venue" />
                        <x-crud.index.delete-button :model="$venue">
                            Are you sure you want to delete venue {{ $venue->name }}?"
                        </x-crud.index.delete-button>
                    </x-crud.index.cell>
                </x-crud.index.row>
            @endforeach
        </x-crud.index.table>

        <div class="mt-4">
            {!! $venues->withQueryString()->links() !!}
        </div>
    </x-crud.content>
</div>
