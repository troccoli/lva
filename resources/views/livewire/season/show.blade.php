<x-crud.header>Seasons</x-crud.header>

<div class="w-full">
    <x-crud.subheader back-route="seasons.index">Details of the {{ $season->name }} season</x-crud.subheader>
    <x-crud.content>
        <x-crud.show.table>
            <x-crud.show.model-field label="Year">{{ $season->year }}</x-crud.show.model-field>
        </x-crud.show.table>
    </x-crud.content>
</div>
