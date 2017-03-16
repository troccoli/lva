<div class="form-group {{ $errors->has('fixture_id') ? 'has-error' : ''}}">
    {!! Form::label('fixture_id', 'Fixture: ', ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-6">
        {!! Form::select('fixture_id', $fixturesSelect, null, ['class' => 'form-control', 'required' => true]) !!}
        {!! $errors->first('fixture_id', '<p class="help-block">:message</p>') !!}
    </div>
</div>

<div class="form-group {{ $errors->has('role_id') ? 'has-error' : ''}}">
    {!! Form::label('role_id', 'Role: ', ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-6">
        {!! Form::select('role_id', array_column($roles->toArray(), 'role', 'id'), null, ['class' => 'form-control', 'required' => true]) !!}
        {!! $errors->first('role_id', '<p class="help-block">:message</p>') !!}
    </div>
</div>

<div class="form-group">
    <div class="col-sm-offset-3 col-sm-3">
        {!! Form::submit($submitText, ['class' => 'btn btn-primary form-control']) !!}
    </div>
</div>