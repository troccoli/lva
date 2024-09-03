<x-crud.header>Venues</x-crud.header>

<div class="w-full">
    <x-crud.subheader back-route="venues.index">Update the {{ $this->form->venueModel->name }} venue</x-crud.subheader>

    <x-crud.content class="mt-8 max-w-xl">
        <form method="POST" wire:submit="save" role="form" enctype="multipart/form-data">
            {{ method_field('PATCH') }}
            @csrf
            @include('livewire.venue.form')
        </form>
    </x-crud.content>
</div>
