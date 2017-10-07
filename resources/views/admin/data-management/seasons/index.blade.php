@extends('layouts.app')

@section('content')

<div class="container-fluid">
    <h1>Seasons <a href="{{ url('admin/data-management/seasons/create') }}"
                   class="btn btn-primary pull-right btn-sm">New season</a>
    </h1>
    <div id="resources-list" class="table">
        <table class="table table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <th>Season</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @foreach($seasons as $season)
                <tr>
                    <td><a href="{{ url('admin/data-management/seasons', $season->id) }}">{{ $season->season }}</a></td>
                    <td>
                        <a href="{{ url('admin/data-management/seasons/' . $season->id . '/edit') }}">
                            <button type="submit" class="btn btn-primary btn-xs">Update</button>
                        </a>
                        {!! Form::open([
                            'method'=>'DELETE',
                            'url' => ['admin/data-management/seasons', $season->id],
                            'style' => 'display:inline'
                        ]) !!}
                            {!! Form::submit('Delete', ['class' => 'btn btn-danger btn-xs', 'data-toggle' => 'confirmation']) !!}
                        {!! Form::close() !!}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="pagination"> {!! $seasons->render() !!} </div>
    </div>
</div>

@endsection

@section('javascript')
    <script src="{{ url('libraries/bootstrap-confirmation.2.4.0.min.js') }}"></script>
    <script src="{{ mix('js/confirm-delete.js') }}"></script>
@endsection