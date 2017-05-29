<div class="form-group {{ $errors->has('venue') ? 'has-error' : ''}}">
    {!! Form::label('venue', 'Venue: ', ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-6">
        {!! Form::text('venue', null, ['class' => 'form-control', 'required' => true, 'autofocus' => true]) !!}
        {!! $errors->first('venue', '<p class="help-block">:message</p>') !!}
    </div>
</div>

<div class="form-group {{ $errors->has('directions') ? 'has-error' : ''}}">
    {!! Form::label('directions', 'Directions: ', ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-6">
        {!! Form::textarea('directions', null, ['class' => 'form-control']) !!}
        {!! $errors->first('directions', '<p class="help-block">:message</p>') !!}
    </div>
</div>

<div class="form-group">
    <div class="col-sm-offset-3 col-sm-3">
        {!! Form::submit($submitText, ['class' => 'btn btn-primary form-control']) !!}
    </div>
</div>
