<div class="form-group {{ $errors->has('username') ? 'has-error' : ''}}">
    <label for="username" class="control-label">{{ 'Username' }}</label>
    <input class="form-control" name="username" type="text" id="username" value="{{ isset($radcheck->username) ? $radcheck->username : ''}}" >
    {!! $errors->first('username', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('attribute') ? 'has-error' : ''}}">
    <label for="attribute" class="control-label">{{ 'Attribute' }}</label>
    <input class="form-control" name="attribute" type="text" id="attribute" value="{{ isset($radcheck->attribute) ? $radcheck->attribute : ''}}" >
    {!! $errors->first('attribute', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('op') ? 'has-error' : ''}}">
    <label for="op" class="control-label">{{ 'Op' }}</label>
    <input class="form-control" name="op" type="text" id="op" value="{{ isset($radcheck->op) ? $radcheck->op : ''}}" >
    {!! $errors->first('op', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('value') ? 'has-error' : ''}}">
    <label for="value" class="control-label">{{ 'Value' }}</label>
    <input class="form-control" name="value" type="text" id="value" value="{{ isset($radcheck->value) ? $radcheck->value : ''}}" >
    {!! $errors->first('value', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('protocol') ? 'has-error' : ''}}">
    <label for="protocol" class="control-label">{{ 'Protocol' }}</label>
    <input class="form-control" name="protocol" type="text" id="protocol" value="{{ isset($radcheck->protocol) ? $radcheck->protocol : ''}}" >
    {!! $errors->first('protocol', '<p class="help-block">:message</p>') !!}
</div>


<div class="form-group">
    <input class="btn btn-primary" type="submit" value="{{ $formMode === 'edit' ? 'Update' : 'Create' }}">
</div>
