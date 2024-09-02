<x-crud.header>Clubs</x-crud.header>

<div class="w-full">
    <x-crud.subheader add-route="clubs.create" class="mb-4">
        A list of all the clubs in the system
    </x-crud.subheader>
    <x-crud.content>
        <x-crud.index.table columns="name,venue">
            @foreach ($clubs as $club)
                <x-crud.index.row row-key="{{ $club->getKey() }}">
                    <x-crud.index.cell>{{ $club->name }}</x-crud.index.cell>
                    <x-crud.index.cell>{{ $club->venue->name }}</x-crud.index.cell>
                    <x-crud.index.cell class="flex gap-1 pl-2 pr-0 font-medium">
                        <x-crud.index.show-button route="clubs.show" :model="$club" />
                        <x-crud.index.edit-button route="clubs.edit" :model="$club" />
                        <x-crud.index.delete-button :model="$club">
                            Are you sure you want to delete the {{ $club->name }} club?
                        </x-crud.index.delete-button>
                    </x-crud.index.cell>
                </x-crud.index.row>
            @endforeach
        </x-crud.index.table>

        <div class="mt-4 px-4">
            {!! $clubs->withQueryString()->links() !!}
        </div>
    </x-crud.content>
</div>
