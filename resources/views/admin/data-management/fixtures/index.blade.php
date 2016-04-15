@extends('layouts.app')

@section('content')

    <div class="container-fluid">
        <h1>Fixtures <a href="{{ url('admin/data-management/fixtures/create') }}"
                        class="btn btn-primary pull-right btn-sm">Add New Fixture</a></h1>
        <div class="table">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                <tr>
                    <th></th>
                    <th>Date</th>
                    <th>Warm-up time</th>
                    <th>Start time</th>
                    <th>Home</th>
                    <th>Away</th>
                    <th>Venue</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($fixtures as $item)
                    <tr>
                        <td>
                            <a href="{{ url('admin/data-management/fixtures', $item->id) }}">
                                {{ $item->division->season->season }} {{ $item->division->division }} {{ $item->match_number }}
                            </a>
                        </td>
                        <td>{{ $item->match_date->format('j M Y') }}</td>
                        <td>{{ $item->warm_up_time }}</td>
                        <td>{{ $item->start_time }}</td>
                        <td>{{ $item->home_team->team }}</td>
                        <td>{{ $item->away_team->team }}</td>
                        <td>{{ $item->venue->venue }}</td>
                        <td>
                            <a href="{{ url('admin/data-management/fixtures/' . $item->id . '/edit') }}">
                                <button type="submit" class="btn btn-primary btn-xs">Update</button>
                            </a> /
                            {!! Form::open([
                                'method'=>'DELETE',
                                'url' => ['admin/data-management/fixtures', $item->id],
                                'style' => 'display:inline'
                            ]) !!}
                            {!! Form::submit('Delete', ['class' => 'btn btn-danger btn-xs', 'data-toggle' => 'confirmation']) !!}
                            {!! Form::close() !!}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="pagination"> {!! $fixtures->render() !!} </div>
        </div>
    </div>

@endsection

@section('javascript')
    <script src="{{ url('js/libraries/bootstrap-confirmation.min.js') }}"></script>
    <script src="{{ url(elixir('js/confirm-delete.js')) }}"></script>
@endsection