<form method="POST" action="{{ $slot }}" style="display: inline" data-action="toBeConfirmed">
    @csrf
    @method('DELETE')
    <button class="btn btn-danger btn-sm" type="submit">{{ __('Delete') }}</button>
</form>
