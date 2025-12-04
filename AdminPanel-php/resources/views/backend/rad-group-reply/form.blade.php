<div class="form-group {{ $errors->has('groupname') ? 'has-error' : ''}}">
    <label for="groupname" class="control-label">{{ 'Groupname' }}</label>
    <input class="form-control" name="groupname" type="text" id="groupname" value="{{ isset($radgroupreply->groupname) ? $radgroupreply->groupname : ''}}" >
    {!! $errors->first('groupname', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('attribute') ? 'has-error' : ''}}">
    <label for="attribute" class="control-label">{{ 'Attribute' }}</label>
    <input class="form-control" name="attribute" type="text" id="attribute" value="{{ isset($radgroupreply->attribute) ? $radgroupreply->attribute : ''}}" >
    {!! $errors->first('attribute', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('op') ? 'has-error' : ''}}">
    <label for="op" class="control-label">{{ 'Op' }}</label>
    <input class="form-control" name="op" type="text" id="op" value="{{ isset($radgroupreply->op) ? $radgroupreply->op : ''}}" >
    {!! $errors->first('op', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('value') ? 'has-error' : ''}}">
    <label for="value" class="control-label">{{ 'Value' }}</label>
    <input class="form-control" name="value" type="text" id="value" value="{{ isset($radgroupreply->value) ? $radgroupreply->value : ''}}" >
    {!! $errors->first('value', '<p class="help-block">:message</p>') !!}
</div>


<div class="form-group">
    <input class="btn btn-primary" type="submit" value="{{ $formMode === 'edit' ? 'Update' : 'Create' }}">
</div>
