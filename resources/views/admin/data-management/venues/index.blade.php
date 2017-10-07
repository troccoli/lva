@extends('layouts.app')

@section('content')

    <div class="container-fluid">
        <h1>Venues <a href="{{ url('admin/data-management/venues/create') }}"
                      class="btn btn-primary pull-right btn-sm">New venue</a>
        </h1>
        <div id="resources-list" class="table">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                <tr>
                    <th>Venue</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($venues as $venue)
                    <tr>
                        <td><a href="{{ url('admin/data-management/venues', $venue->id) }}">{{ $venue->venue }}</a></td>
                        <td>
                            <a href="{{ url('admin/data-management/venues/' . $venue->id . '/edit') }}">
                                <button type="submit" class="btn btn-primary btn-xs">Update</button>
                            </a>
                            {!! Form::open([
                                'method'=>'DELETE',
                                'url' => ['admin/data-management/venues', $venue->id],
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
    <script src="{{ url('libraries/bootstrap-confirmation.2.4.0.min.js') }}"></script>
    <script src="{{ mix('js/confirm-delete.js') }}"></script>
@endsection