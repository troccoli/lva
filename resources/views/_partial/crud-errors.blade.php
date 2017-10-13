@if ($errors->any())
    <div id="form-errors" class="alert alert-danger">
        @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif