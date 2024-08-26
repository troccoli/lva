<x-crud.header>Seasons</x-crud.header>

<div class="w-full">
    <x-crud.subheader back-route="seasons.index">Create a new season</x-crud.subheader>

    <x-crud.content class="mt-8 max-w-xl">
        <form method="POST" wire:submit="save" role="form" enctype="multipart/form-data">
            @csrf
            @include('livewire.season.form')
        </form>
    </x-crud.content>
</div>
