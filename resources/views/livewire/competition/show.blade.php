<x-crud.header>Competitions</x-crud.header>

<div class="w-full">
    <x-crud.subheader back-route="competitions.index">Details of the {{ $competition->name }} competition</x-crud.subheader>
    <x-crud.content>
        <x-crud.show.table>
            <x-crud.show.model-field label="Season">{{ $competition->season->name }}</x-crud.show.model-field>
            <x-crud.show.model-field label="Name">{{ $competition->name }}</x-crud.show.model-field>
        </x-crud.show.table>
    </x-crud.content>
</div>
