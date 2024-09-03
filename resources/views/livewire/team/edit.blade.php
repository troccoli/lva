<x-crud.header>Teams</x-crud.header>

<div class="w-full">
    <x-crud.subheader back-route="teams.index">Update the {{ $this->form->teamModel->name }} team</x-crud.subheader>

    <x-crud.content class="mt-8 max-w-xl">
        <form method="POST" wire:submit="save" role="form" enctype="multipart/form-data">
            {{ method_field('PATCH') }}
            @csrf
            @include('livewire.team.form')
        </form>
    </x-crud.content>
</div>
