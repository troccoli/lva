<x-crud.header>Clubs</x-crud.header>

<div class="w-full">
    <x-crud.subheader back-route="clubs.index">Update the {{ $form->name }} club</x-crud.subheader>

    <x-crud.content class="mt-8 max-w-xl">
        <form method="POST" wire:submit="save" role="form" enctype="multipart/form-data">
            {{ method_field('PATCH') }}
            @csrf
            @include('livewire.club.form')
        </form>
    </x-crud.content>
</div>
