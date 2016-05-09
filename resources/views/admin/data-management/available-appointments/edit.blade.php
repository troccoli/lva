@extends('admin.data-management.home')

@section('crud')

    <?php
    $fixturesSelect = [];
    foreach ($fixtures as $fixture) {
        $fixturesSelect[$fixture->id] = $fixture;
    }
    ?>

    <div class="container-fluid">
        <h1>Edit appointment</h1>
        <hr/>

        @include('_partial.crud-errors')

        {!! Form::model($availableAppointment, [
            'method' => 'PATCH',
            'url' => ['admin/data-management/available-appointments', $availableAppointment->id],
            'class' => 'form-horizontal'
        ]) !!}

        {!! Form::hidden('id', $availableAppointment->id) !!}

        <div class="form-group {{ $errors->has('fixture_id') ? 'has-error' : ''}}">
            {!! Form::label('fixture_id', 'Fixture: ', ['class' => 'col-sm-3 control-label']) !!}
            <div class="col-sm-6">
                {!! Form::select('fixture_id', $fixturesSelect, null, ['class' => 'form-control', 'required' => 'required']) !!}
                {!! $errors->first('fixture_id', '<p class="help-block">:message</p>') !!}
            </div>
        </div>

        <div class="form-group {{ $errors->has('role_id') ? 'has-error' : ''}}">
            {!! Form::label('role_id', 'Role: ', ['class' => 'col-sm-3 control-label']) !!}
            <div class="col-sm-6">
                {!! Form::select('role_id', array_column($roles->toArray(), 'role', 'id'), null, ['class' => 'form-control', 'required' => 'required']) !!}
                {!! $errors->first('role_id', '<p class="help-block">:message</p>') !!}
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-3">
                {!! Form::submit('Update', ['class' => 'btn btn-primary form-control']) !!}
            </div>
        </div>

        {!! Form::close() !!}
    </div>

@endsection