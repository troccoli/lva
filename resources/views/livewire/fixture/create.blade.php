<x-crud.header>Fixtures</x-crud.header>

<div class="w-full">
    <x-crud.subheader back-route="fixtures.index">Create a new fixture</x-crud.subheader>

    <x-crud.content class="mt-8 max-w-xl">
        <form method="POST" wire:submit="save" role="form" enctype="multipart/form-data">
            @csrf
            @include('livewire.fixture.form')
        </form>
    </x-crud.content>
</div>
