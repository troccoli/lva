<div class="form-group {{ $errors->has('season_id') ? 'has-error' : ''}}">
    {!! Form::label('season_id', 'Season: ', ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-6">
        {!! Form::select('season_id', array_column($seasons->toArray(), 'season', 'id'), null, ['class' => 'form-control']) !!}
        {!! $errors->first('season_id', '<p class="help-block">:message</p>') !!}
    </div>
</div>

<div class="form-group {{ $errors->has('division') ? 'has-error' : ''}}">
    {!! Form::label('division', 'Division: ', ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-6">
        {!! Form::text('division', null, ['class' => 'form-control', 'required' => true, 'autofocus' => true]) !!}
        {!! $errors->first('division', '<p class="help-block">:message</p>') !!}
    </div>
</div>

<div class="form-group">
    <div class="col-sm-offset-3 col-sm-3">
        {!! Form::submit($submitText, ['class' => 'btn btn-primary form-control']) !!}
    </div>
</div>