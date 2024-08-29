<x-crud.header>Competitions</x-crud.header>

<div class="w-full">
    <x-crud.subheader add-route="competitions.create" class="mb-4">
        A list of all the competitions in the system
    </x-crud.subheader>
    <x-crud.content>
        <x-crud.index.table columns="season,name">
            @foreach ($competitions as $competition)
                <x-crud.index.row row-key="{{ $competition->getKey() }}">
                    <x-crud.index.cell>{{ $competition->season->name }}</x-crud.index.cell>
                    <x-crud.index.cell>{{ $competition->name }}</x-crud.index.cell>
                    <x-crud.index.cell class="flex gap-1 pl-2 pr-0 font-medium">
                        <x-crud.index.show-button route="competitions.show" :model="$competition" />
                        <x-crud.index.edit-button route="competitions.edit" :model="$competition" />
                        <x-crud.index.delete-button :model="$competition">
                            Are you sure you want to delete the {{ $competition->name }} competition in the {{ $competition->season->name }} season?
                        </x-crud.index.delete-button>
                    </x-crud.index.cell>
                </x-crud.index.row>
            @endforeach
        </x-crud.index.table>

        <div class="mt-4 px-4">
            {!! $competitions->withQueryString()->links() !!}
        </div>
    </x-crud.content>
</div>
