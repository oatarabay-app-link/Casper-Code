<div class="form-group {{ $errors->has('username') ? 'has-error' : ''}}">
    <label for="username" class="control-label">{{ 'Username' }}</label>
    <input class="form-control" name="username" type="text" id="username" value="{{ isset($radpostauth->username) ? $radpostauth->username : ''}}" >
    {!! $errors->first('username', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('pass') ? 'has-error' : ''}}">
    <label for="pass" class="control-label">{{ 'Pass' }}</label>
    <input class="form-control" name="pass" type="text" id="pass" value="{{ isset($radpostauth->pass) ? $radpostauth->pass : ''}}" >
    {!! $errors->first('pass', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('reply') ? 'has-error' : ''}}">
    <label for="reply" class="control-label">{{ 'Reply' }}</label>
    <input class="form-control" name="reply" type="text" id="reply" value="{{ isset($radpostauth->reply) ? $radpostauth->reply : ''}}" >
    {!! $errors->first('reply', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('priority') ? 'has-error' : ''}}">
    <label for="priority" class="control-label">{{ 'Priority' }}</label>
    <input class="form-control" name="priority" type="number" id="priority" value="{{ isset($radpostauth->priority) ? $radpostauth->priority : ''}}" >
    {!! $errors->first('priority', '<p class="help-block">:message</p>') !!}
</div>


<div class="form-group">
    <input class="btn btn-primary" type="submit" value="{{ $formMode === 'edit' ? 'Update' : 'Create' }}">
</div>
