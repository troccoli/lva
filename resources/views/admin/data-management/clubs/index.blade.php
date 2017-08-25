@extends('layouts.app')

@section('content')

    <div class="container-fluid">
        <h1>Clubs <a href="{{ url('admin/data-management/clubs/create') }}" class="btn btn-primary pull-right btn-sm">New
                club</a></h1>
        <div class="table">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                <tr>
                    <th>Club</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($clubs as $club)
                    <tr>
                        <td><a href="{{ url('admin/data-management/clubs', $club->id) }}">{{ $club->club }}</a></td>
                        <td>
                            <a href="{{ url('admin/data-management/clubs/' . $club->id . '/edit') }}">
                                <button type="submit" class="btn btn-primary btn-xs">Update</button>
                            </a>
                            {!! Form::open([
                                'method'=>'DELETE',
                                'url' => ['admin/data-management/clubs', $club->id],
                                'style' => 'display:inline'
                            ]) !!}
                            {!! Form::submit('Delete', ['class' => 'btn btn-danger btn-xs', 'data-toggle' => 'confirmation']) !!}
                            {!! Form::close() !!}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="pagination"> {!! $clubs->render() !!} </div>
        </div>
    </div>

@endsection

@section('javascript')
    <script src="{{ url('libraries/bootstrap-confirmation.2.4.0.min.js') }}"></script>
    <script src="{{ mix('js/confirm-delete.js') }}"></script>
@endsection