@extends('layouts.app')

@section('content')

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
            'route' => ['available-appointments.update', $availableAppointment->id],
            'class' => 'form-horizontal'
        ]) !!}
        {!! Form::hidden('id', $availableAppointment->id) !!}

        @include('admin.data-management.available-appointments._form', ['submitText' => 'Update'])

        {!! Form::close() !!}
    </div>

@endsection