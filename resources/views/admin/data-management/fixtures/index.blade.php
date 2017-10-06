@extends('layouts.app')

@section('content')

    <div class="container-fluid">
        <h1>Fixtures <a href="{{ url('admin/data-management/fixtures/create') }}"
                        class="btn btn-primary pull-right btn-sm">New fixture</a></h1>
        <div class="table">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                <tr>
                    <th></th>
                    <th>Date</th>
                    <th>Warm-up</th>
                    <th>Start</th>
                    <th>Home</th>
                    <th>Away</th>
                    <th>Venue</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($fixtures as $fixture)
                    <tr>
                        <td>
                            <a href="{{ url('admin/data-management/fixtures', $fixture->id) }}">
                                {{ $fixture->division }}:{{ $fixture->match_number }}
                            </a>
                        </td>
                        <td>{{ $fixture->match_date->format('j M Y') }}</td>
                        <td>{{ $fixture->warm_up_time->format('H:i') }}</td>
                        <td>{{ $fixture->start_time->format('H:i') }}</td>
                        <td>{{ $fixture->home_team }}</td>
                        <td>{{ $fixture->away_team }}</td>
                        <td>{{ $fixture->venue }}</td>
                        <td>
                            <a href="{{ url('admin/data-management/fixtures/' . $fixture->id . '/edit') }}">
                                <button type="submit" class="btn btn-primary btn-xs">Update</button>
                            </a>
                            {!! Form::open([
                                'method'=>'DELETE',
                                'url' => ['admin/data-management/fixtures', $fixture->id],
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
    <script src="{{ asset('libraries/bootstrap-confirmation.2.4.0.min.js') }}"></script>
    <script src="{{ mix('js/confirm-delete.js') }}"></script>
@endsection