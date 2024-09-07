<x-crud.header>Fixtures</x-crud.header>

<div class="w-full">
    <x-crud.subheader back-route="fixtures.index">Details of the a fixture</x-crud.subheader>
    <x-crud.content>
        <x-crud.show.table>
            <x-crud.show.model-field label="Season">{{ $fixture->division->competition->season->name }}</x-crud.show.model-field>
            <x-crud.show.model-field label="Competition">{{ $fixture->division->competition->name }}</x-crud.show.model-field>
            <x-crud.show.model-field label="Division">{{ $fixture->division->name }}</x-crud.show.model-field>
            <x-crud.show.model-field label="Match number">{{ $fixture->match_number }}</x-crud.show.model-field>
            <x-crud.show.model-field label="Home team">{{ $fixture->homeTeam->name }}</x-crud.show.model-field>
            <x-crud.show.model-field label="Away team">{{ $fixture->awayTeam->name }}</x-crud.show.model-field>
            <x-crud.show.model-field label="Match date and time">{{ $fixture->matchDatetime->toIsoString() }}</x-crud.show.model-field>
            <x-crud.show.model-field label="Venue">{{ $fixture->venue->name }}</x-crud.show.model-field>
        </x-crud.show.table>
    </x-crud.content>
</div>
