@extends('layouts.app')

@section('content')

<div class="container-fluid">
    <h1>Venues <a href="{{ url('admin/data-management/venues/create') }}" class="btn btn-primary pull-right btn-sm">Add New Venue</a></h1>
    <div class="table">
        <table class="table table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <th>Venue</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @foreach($venues as $item)
                <tr>
                    <td><a href="{{ url('admin/data-management/venues', $item->id) }}">{{ $item->venue }}</a></td>
                    <td>
                        <a href="{{ url('admin/data-management/venues/' . $item->id . '/edit') }}">
                            <button type="submit" class="btn btn-primary btn-xs">Update</button>
                        </a> /
                        {!! Form::open([
                            'method'=>'DELETE',
                            'url' => ['admin/data-management/venues', $item->id],
                            'style' => 'display:inline'
                        ]) !!}
                            {!! Form::submit('Delete', ['class' => 'btn btn-danger btn-xs', 'data-toggle' => 'confirmation']) !!}
                        {!! Form::close() !!}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="pagination"> {!! $venues->render() !!} </div>
    </div>
</div>

@endsection

@section('javascript')
    <script src="{{ url('js/libraries/bootstrap-confirmation.min.js') }}"></script>
    <script src="{{ url(elixir('js/confirm-delete.js')) }}"></script>
@endsection