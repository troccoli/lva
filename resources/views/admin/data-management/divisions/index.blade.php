@extends('layouts.app')

@section('content')

    <div class="container-fluid">
        <h1>Divisions <a href="{{ url('admin/data-management/divisions/create') }}"
                         class="btn btn-primary pull-right btn-sm">New division</a></h1>
        <div class="table">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                <tr>
                    <th>Season</th>
                    <th>Division</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($divisions as $division)
                    <tr>
                        <td>{{ $division->season->season }}</td>
                        <td>
                            <a href="{{ url('admin/data-management/divisions', $division->id) }}">{{ $division->division }}</a>
                        </td>
                        <td>
                            <a href="{{ url('admin/data-management/divisions/' . $division->id . '/edit') }}">
                                <button type="submit" class="btn btn-primary btn-xs">Update</button>
                            </a>
                            {!! Form::open([
                                'method'=>'DELETE',
                                'url' => ['admin/data-management/divisions', $division->id],
                                'style' => 'display:inline'
                            ]) !!}
                            {!! Form::submit('Delete', ['class' => 'btn btn-danger btn-xs', 'data-toggle' => 'confirmation']) !!}
                            {!! Form::close() !!}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="pagination"> {!! $divisions->render() !!} </div>
        </div>
    </div>

@endsection

@section('javascript')
    <script src="{{ url('libraries/bootstrap-confirmation.2.4.0.min.js') }}"></script>
    <script src="{{ url(elixir('js/confirm-delete.js')) }}"></script>
@endsection