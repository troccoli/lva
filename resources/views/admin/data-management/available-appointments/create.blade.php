@extends('layouts.app')

@section('content')

    <?php
    $fixturesSelect = [];
    foreach ($fixtures as $fixture) {
        $fixturesSelect[$fixture->id] = $fixture;
    }
    ?>
    <div class="container-fluid">
        <h1>Add new appointment</h1>
        <hr/>

        @include('_partial.crud-errors')

        {!! Form::open(['route' => 'admin.data-management.available-appointments.store', 'class' => 'form-horizontal']) !!}

        @include('admin.data-management.available-appointments._form', ['submitText' => 'Add'])

        {!! Form::close() !!}
    </div>

@endsection