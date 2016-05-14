@extends('admin.data-management.home')

@section('crud')

    <div class="container-fluid">
        <h1>Teams <a href="{{ url('admin/data-management/teams/create') }}" class="btn btn-primary pull-right btn-sm">New
                team</a></h1>
        <div class="table">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                <tr>
                    <th>Club</th>
                    <th>Team</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($teams as $team)
                    <tr>
                        <td>{{ $team->club->club }}</td>
                        <td><a href="{{ url('admin/data-management/teams', $team->id) }}">{{ $team->team }}</a></td>
                        <td>
                            <a href="{{ url('admin/data-management/teams/' . $team->id . '/edit') }}">
                                <button type="submit" class="btn btn-primary btn-xs">Update</button>
                            </a> /
                            {!! Form::open([
                                'method'=>'DELETE',
                                'url' => ['admin/data-management/teams', $team->id],
                                'style' => 'display:inline'
                            ]) !!}
                            {!! Form::submit('Delete', ['class' => 'btn btn-danger btn-xs', 'data-toggle' => 'confirmation']) !!}
                            {!! Form::close() !!}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="pagination"> {!! $teams->render() !!} </div>
        </div>
    </div>

@endsection

@section('javascript')
    <script src="{{ url('js/libraries/bootstrap-confirmation.min.js') }}"></script>
    <script src="{{ url(elixir('js/confirm-delete.js')) }}"></script>
@endsection