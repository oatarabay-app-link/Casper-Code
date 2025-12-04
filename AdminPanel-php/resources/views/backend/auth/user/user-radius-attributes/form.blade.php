<div class="form-group {{ $errors->has('user_id') ? 'has-error' : ''}}">
    <label for="user_id" class="control-label">{{ 'User Id' }}</label>
    <input class="form-control" name="user_id" type="number" id="user_id" value="{{ isset($userradiusattribute->user_id) ? $userradiusattribute->user_id : ''}}" >
    {!! $errors->first('user_id', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('attribute') ? 'has-error' : ''}}">
    <label for="attribute" class="control-label">{{ 'Attribute' }}</label>
    <input class="form-control" name="attribute" type="text" id="attribute" value="{{ isset($userradiusattribute->attribute) ? $userradiusattribute->attribute : ''}}" >
    {!! $errors->first('attribute', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('op') ? 'has-error' : ''}}">
    <label for="op" class="control-label">{{ 'Op' }}</label>
    <input class="form-control" name="op" type="text" id="op" value="{{ isset($userradiusattribute->op) ? $userradiusattribute->op : ''}}" >
    {!! $errors->first('op', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('value') ? 'has-error' : ''}}">
    <label for="value" class="control-label">{{ 'Value' }}</label>
    <input class="form-control" name="value" type="text" id="value" value="{{ isset($userradiusattribute->value) ? $userradiusattribute->value : ''}}" >
    {!! $errors->first('value', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('description') ? 'has-error' : ''}}">
    <label for="description" class="control-label">{{ 'Description' }}</label>
    <textarea class="form-control" rows="5" name="description" type="textarea" id="description" >{{ isset($userradiusattribute->description) ? $userradiusattribute->description : ''}}</textarea>
    {!! $errors->first('description', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('status') ? 'has-error' : ''}}">
    <label for="status" class="control-label">{{ 'Status' }}</label>
    <div class="radio">
    <label><input name="status" type="radio" value="1" {{ (isset($userradiusattribute) && 1 == $userradiusattribute->status) ? 'checked' : '' }}> Yes</label>
</div>
<div class="radio">
    <label><input name="status" type="radio" value="0" @if (isset($userradiusattribute)) {{ (0 == $userradiusattribute->status) ? 'checked' : '' }} @else {{ 'checked' }} @endif> No</label>
</div>
    {!! $errors->first('status', '<p class="help-block">:message</p>') !!}
</div>


<div class="form-group">
    <input class="btn btn-primary" type="submit" value="{{ $formMode === 'edit' ? 'Update' : 'Create' }}">
</div>
