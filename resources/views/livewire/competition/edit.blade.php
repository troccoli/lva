<div class="w-full">
    <x-crud.header>Competitions</x-crud.header>
    <x-crud.subheader back>
        Update the {{ $this->form->competitionModel->name }} competition
    </x-crud.subheader>

    <x-crud.content class="mt-8 max-w-xl">
        <form method="POST" wire:submit="save" role="form" enctype="multipart/form-data">
            {{ method_field('PATCH') }}
            @csrf
            @include('livewire.competition.form')
        </form>
    </x-crud.content>
</div>
