<div class="form-group {{ $errors->has('username') ? 'has-error' : ''}}">
    <label for="username" class="control-label">{{ 'Username' }}</label>
    <input class="form-control" name="username" type="text" id="username" value="{{ isset($radusergroup->username) ? $radusergroup->username : ''}}" >
    {!! $errors->first('username', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('groupname') ? 'has-error' : ''}}">
    <label for="groupname" class="control-label">{{ 'Groupname' }}</label>
    <input class="form-control" name="groupname" type="text" id="groupname" value="{{ isset($radusergroup->groupname) ? $radusergroup->groupname : ''}}" >
    {!! $errors->first('groupname', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('priority') ? 'has-error' : ''}}">
    <label for="priority" class="control-label">{{ 'Priority' }}</label>
    <input class="form-control" name="priority" type="number" id="priority" value="{{ isset($radusergroup->priority) ? $radusergroup->priority : ''}}" >
    {!! $errors->first('priority', '<p class="help-block">:message</p>') !!}
</div>


<div class="form-group">
    <input class="btn btn-primary" type="submit" value="{{ $formMode === 'edit' ? 'Update' : 'Create' }}">
</div>
