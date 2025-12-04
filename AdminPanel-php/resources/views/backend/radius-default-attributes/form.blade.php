<div class="form-group {{ $errors->has('attribute') ? 'has-error' : ''}}">
    <label for="attribute" class="control-label">{{ 'Attribute' }}</label>
    <input class="form-control" name="attribute" type="text" id="attribute" value="{{ isset($radiusdefaultattribute->attribute) ? $radiusdefaultattribute->attribute : ''}}" >
    {!! $errors->first('attribute', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('op') ? 'has-error' : ''}}">
    <label for="op" class="control-label">{{ 'Op' }}</label>
    <input class="form-control" name="op" type="text" id="op" value="{{ isset($radiusdefaultattribute->op) ? $radiusdefaultattribute->op : ''}}" >
    {!! $errors->first('op', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('value') ? 'has-error' : ''}}">
    <label for="value" class="control-label">{{ 'Value' }}</label>
    <input class="form-control" name="value" type="text" id="value" value="{{ isset($radiusdefaultattribute->value) ? $radiusdefaultattribute->value : ''}}" >
    {!! $errors->first('value', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('description') ? 'has-error' : ''}}">
    <label for="description" class="control-label">{{ 'Description' }}</label>
    <textarea class="form-control" rows="5" name="description" type="textarea" id="description" >{{ isset($radiusdefaultattribute->description) ? $radiusdefaultattribute->description : ''}}</textarea>
    {!! $errors->first('description', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('status') ? 'has-error' : ''}}">
    <label for="status" class="control-label">{{ 'Status' }}</label>
    <div class="radio">
    <label><input name="status" type="radio" value="1" {{ (isset($radiusdefaultattribute) && 1 == $radiusdefaultattribute->status) ? 'checked' : '' }}> Yes</label>
</div>
<div class="radio">
    <label><input name="status" type="radio" value="0" @if (isset($radiusdefaultattribute)) {{ (0 == $radiusdefaultattribute->status) ? 'checked' : '' }} @else {{ 'checked' }} @endif> No</label>
</div>
    {!! $errors->first('status', '<p class="help-block">:message</p>') !!}
</div>


<div class="form-group">
    <input class="btn btn-primary" type="submit" value="{{ $formMode === 'edit' ? 'Update' : 'Create' }}">
</div>
