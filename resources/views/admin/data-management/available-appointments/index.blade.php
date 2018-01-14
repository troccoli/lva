@extends('layouts.app')

@section('content')

    <div class="container-fluid">
        <h1>Available appointments <a href="{{ url('admin/data-management/available-appointments/create') }}"
                                      class="btn btn-primary pull-right btn-sm">New appointment</a>
        </h1>
        <div id="resources-list" class="table">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                <tr>
                    <th>Fixture</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($availableAppointments as $appointment)
                    <tr>
                        <td>
                            <a href="{{ url('admin/data-management/available-appointments', $appointment->id) }}">
                                {{ $appointment->fixture }}
                            </a>
                        </td>
                        <td>{{ $appointment->role->role }}</td>
                        <td>
                            <a href="{{ url('admin/data-management/available-appointments/' . $appointment->id . '/edit') }}">
                                <button type="submit" class="btn btn-primary btn-xs">Update</button>
                            </a>
                            {!! Form::open([
                                'method'=>'DELETE',
                                'url' => ['admin/data-management/available-appointments', $appointment->id],
                                'style' => 'display:inline'
                            ]) !!}
                            {!! Form::submit('Delete', ['class' => 'btn btn-danger btn-xs', 'data-toggle' => 'confirmation']) !!}
                            {!! Form::close() !!}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="pagination"> {!! $availableAppointments->render() !!} </div>
        </div>
    </div>

@endsection

@section('javascript')
    <script src="{{ url('libraries/bootstrap-confirmation.2.4.0.min.js') }}"></script>
    <script src="{{ mix('js/confirm-delete.js') }}"></script>
@endsection