@extends('admin.data-management.home')

@section('crud')

    <div class="container-fluid">
        <h1>Add a new club</h1>
        <hr/>

        @include('_partial.crud-errors')

        {!! Form::open(['url' => 'admin/data-management/clubs', 'class' => 'form-horizontal']) !!}

        <div class="form-group {{ $errors->has('club') ? 'has-error' : ''}}">
            {!! Form::label('club', 'Club: ', ['class' => 'col-sm-3 control-label']) !!}
            <div class="col-sm-6">
                {!! Form::text('club', null, ['class' => 'form-control']) !!}
                {!! $errors->first('club', '<p class="help-block">:message</p>') !!}
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-3">
                {!! Form::submit('Add', ['class' => 'btn btn-primary form-control']) !!}
            </div>
        </div>

        {!! Form::close() !!}
    </div>

@endsection