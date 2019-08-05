<form method="POST" action="{{ $slot }}" style="display: inline" data-action="toBeConfirmed">
    @csrf
    @method('DELETE')
    <button class="btn btn-danger btn-sm" type="submit">
        <i class="far fa-trash-alt"></i><span class="sr-only">{{ __('Delete') }}</span>
    </button>
</form>
