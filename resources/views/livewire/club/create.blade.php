<div class="w-full">
    <x-crud.header>Clubs</x-crud.header>
    <x-crud.subheader back>
        Create a new club
    </x-crud.subheader>

    <x-crud.content class="mt-8 max-w-xl">
        <form method="POST" wire:submit="save" role="form" enctype="multipart/form-data">
            @csrf
            @include('livewire.club.form')
        </form>
    </x-crud.content>
</div>
