<div id="club-field" class="form-group {{ $errors->has('club') ? 'has-error' : ''}}">
    {!! Form::label('club', 'Club: ', ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-6">
        {!! Form::text('club', null, ['class' => 'form-control', 'autofocus' => true]) !!}
        {!! $errors->first('club', '<p class="help-block">:message</p>') !!}
    </div>
</div>

<div class="form-group">
    <div class="col-sm-offset-3 col-sm-3">
        {!! Form::submit($submitText, ['class' => 'btn btn-primary form-control']) !!}
    </div>
</div>