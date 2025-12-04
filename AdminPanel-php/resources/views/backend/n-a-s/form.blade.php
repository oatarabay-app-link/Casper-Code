<div class="form-group {{ $errors->has('nasname') ? 'has-error' : ''}}">
    <label for="nasname" class="control-label">{{ 'Nasname' }}</label>
    <input class="form-control" name="nasname" type="text" id="nasname" value="{{ isset($na->nasname) ? $na->nasname : ''}}" >
    {!! $errors->first('nasname', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('shortname') ? 'has-error' : ''}}">
    <label for="shortname" class="control-label">{{ 'Shortname' }}</label>
    <input class="form-control" name="shortname" type="text" id="shortname" value="{{ isset($na->shortname) ? $na->shortname : ''}}" >
    {!! $errors->first('shortname', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('type') ? 'has-error' : ''}}">
    <label for="type" class="control-label">{{ 'Type' }}</label>
    <input class="form-control" name="type" type="text" id="type" value="{{ isset($na->type) ? $na->type : ''}}" >
    {!! $errors->first('type', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('ports') ? 'has-error' : ''}}">
    <label for="ports" class="control-label">{{ 'Ports' }}</label>
    <input class="form-control" name="ports" type="number" id="ports" value="{{ isset($na->ports) ? $na->ports : ''}}" >
    {!! $errors->first('ports', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('secret') ? 'has-error' : ''}}">
    <label for="secret" class="control-label">{{ 'Secret' }}</label>
    <input class="form-control" name="secret" type="text" id="secret" value="{{ isset($na->secret) ? $na->secret : ''}}" >
    {!! $errors->first('secret', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('server') ? 'has-error' : ''}}">
    <label for="server" class="control-label">{{ 'Server' }}</label>
    <input class="form-control" name="server" type="text" id="server" value="{{ isset($na->server) ? $na->server : ''}}" >
    {!! $errors->first('server', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('community') ? 'has-error' : ''}}">
    <label for="community" class="control-label">{{ 'Community' }}</label>
    <input class="form-control" name="community" type="text" id="community" value="{{ isset($na->community) ? $na->community : ''}}" >
    {!! $errors->first('community', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('description') ? 'has-error' : ''}}">
    <label for="description" class="control-label">{{ 'Description' }}</label>
    <input class="form-control" name="description" type="text" id="description" value="{{ isset($na->description) ? $na->description : ''}}" >
    {!! $errors->first('description', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('details') ? 'has-error' : ''}}">
    <label for="details" class="control-label">{{ 'Details' }}</label>
    <textarea class="form-control" rows="5" name="details" type="textarea" id="details" >{{ isset($na->details) ? $na->details : ''}}</textarea>
    {!! $errors->first('details', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('check_code') ? 'has-error' : ''}}">
    <label for="check_code" class="control-label">{{ 'Check Code' }}</label>
    <textarea class="form-control" rows="5" name="check_code" type="textarea" id="check_code" >{{ isset($na->check_code) ? $na->check_code : ''}}</textarea>
    {!! $errors->first('check_code', '<p class="help-block">:message</p>') !!}
</div>


<div class="form-group">
    <input class="btn btn-primary" type="submit" value="{{ $formMode === 'edit' ? 'Update' : 'Create' }}">
</div>
