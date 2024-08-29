<x-crud.header>Competitions</x-crud.header>

<div class="w-full">
    <x-crud.subheader back-route="competitions.index">Create a new competition</x-crud.subheader>

    <x-crud.content class="mt-8 max-w-xl">
        <form method="POST" wire:submit="save" role="form" enctype="multipart/form-data">
            @csrf
            @include('livewire.competition.form')
        </form>
    </x-crud.content>
</div>
