<div class="w-full">
    <x-crud.header>Seasons</x-crud.header>
    <x-crud.subheader back>
        Create a new season
    </x-crud.subheader>

    <x-crud.content class="mt-8 max-w-xl">
        <form method="POST" wire:submit="save" role="form" enctype="multipart/form-data">
            @csrf
            @include('livewire.season.form')
        </form>
    </x-crud.content>
</div>
