<div id="season-field" class="form-group {{ $errors->has('season') ? 'has-error' : ''}}">
    {!! Form::label('season', 'Season: ', ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-6">
        {!! Form::text('season', null, ['class' => 'form-control', 'autofocus' => true]) !!}
        {!! $errors->first('season', '<p class="help-block">:message</p>') !!}
    </div>
</div>

<div class="form-group">
    <div class="col-sm-offset-3 col-sm-3">
        {!! Form::submit($submitText, ['class' => 'btn btn-primary form-control']) !!}
    </div>
</div>