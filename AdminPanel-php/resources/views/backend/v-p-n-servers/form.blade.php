<div class="row">
    <div class="col-sm-6">

        <div class="form-group {{ $errors->has('uuid') ? 'has-error' : ''}}">
    <label for="uuid" class="control-label">{{ 'Uuid' }}</label>
    <input class="form-control" name="uuid" type="text" id="uuid" value="{{ isset($vpnserver->uuid) ? $vpnserver->uuid : ''}}" >
    {!! $errors->first('uuid', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('create_date') ? 'has-error' : ''}}">
    <label for="create_date" class="control-label">{{ 'Create Date' }}</label>
    <input class="form-control" name="create_date" type="datetime-local" id="create_date" value="{{ isset($vpnserver->create_date) ? $vpnserver->create_date : ''}}" >
    {!! $errors->first('create_date', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('is_deleted') ? 'has-error' : ''}}">
    <label for="is_deleted" class="control-label">{{ 'Is Deleted' }}</label>
    <div class="radio">
    <label><input name="is_deleted" type="radio" value="1" {{ (isset($vpnserver) && 1 == $vpnserver->is_deleted) ? 'checked' : '' }}> Yes</label>
</div>
<div class="radio">
    <label><input name="is_deleted" type="radio" value="0" @if (isset($vpnserver)) {{ (0 == $vpnserver->is_deleted) ? 'checked' : '' }} @else {{ 'checked' }} @endif> No</label>
</div>
    {!! $errors->first('is_deleted', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('is_disabled') ? 'has-error' : ''}}">
    <label for="is_disabled" class="control-label">{{ 'Is Disabled' }}</label>
    <div class="radio">
    <label><input name="is_disabled" type="radio" value="1" {{ (isset($vpnserver) && 1 == $vpnserver->is_disabled) ? 'checked' : '' }}> Yes</label>
</div>
<div class="radio">
    <label><input name="is_disabled" type="radio" value="0" @if (isset($vpnserver)) {{ (0 == $vpnserver->is_disabled) ? 'checked' : '' }} @else {{ 'checked' }} @endif> No</label>
</div>
    {!! $errors->first('is_disabled', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('ip') ? 'has-error' : ''}}">
    <label for="ip" class="control-label">{{ 'Ip' }}</label>
    <input class="form-control" name="ip" type="text" id="ip" value="{{ isset($vpnserver->ip) ? $vpnserver->ip : ''}}" >
    {!! $errors->first('ip', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('latitude') ? 'has-error' : ''}}">
    <label for="latitude" class="control-label">{{ 'Latitude' }}</label>
    <input class="form-control" name="latitude" type="text" id="latitude" value="{{ isset($vpnserver->latitude) ? $vpnserver->latitude : ''}}" >
    {!! $errors->first('latitude', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('longitude') ? 'has-error' : ''}}">
    <label for="longitude" class="control-label">{{ 'Longitude' }}</label>
    <input class="form-control" name="longitude" type="text" id="longitude" value="{{ isset($vpnserver->longitude) ? $vpnserver->longitude : ''}}" >
    {!! $errors->first('longitude', '<p class="help-block">:message</p>') !!}
</div>


</div>

<div class="col-sm-6">
    
<div class="form-group {{ $errors->has('name') ? 'has-error' : ''}}">
    <label for="name" class="control-label">{{ 'Name' }}</label>
    <input class="form-control" name="name" type="text" id="name" value="{{ isset($vpnserver->name) ? $vpnserver->name : ''}}" >
    {!! $errors->first('name', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('country') ? 'has-error' : ''}}">
    <label for="country" class="control-label">{{ 'Country' }}</label>
    <input class="form-control" name="country" type="text" id="country" value="{{ isset($vpnserver->country) ? $vpnserver->country : ''}}" >
    {!! $errors->first('country', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('parameters') ? 'has-error' : ''}}">
    <label for="parameters" class="control-label">{{ 'Parameters' }}</label>
    <textarea class="form-control" rows="5" name="parameters" type="textarea" id="parameters" >{{ isset($vpnserver->parameters) ? $vpnserver->parameters : ''}}</textarea>
    {!! $errors->first('parameters', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('server_provider') ? 'has-error' : ''}}">
    <label for="server_provider" class="control-label">{{ 'Server Provider' }}</label>
    <input class="form-control" name="server_provider" type="text" id="server_provider" value="{{ isset($vpnserver->server_provider) ? $vpnserver->server_provider : ''}}" >
    {!! $errors->first('server_provider', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('notes') ? 'has-error' : ''}}">
    <label for="notes" class="control-label">{{ 'Notes' }}</label>
    <textarea class="form-control" rows="5" name="notes" type="textarea" id="notes" >{{ isset($vpnserver->notes) ? $vpnserver->notes : ''}}</textarea>
    {!! $errors->first('notes', '<p class="help-block">:message</p>') !!}
</div>

<div class="form-group {{ $errors->has('service_id') ? 'has-error' : ''}}">
    <label for="service_id" class="control-label">{{ 'Service ID' }}</label>
    <input class="form-control" name="service_id" type="number" id="service_id" value="{{ isset($vpnserver->service_id) ? $vpnserver->service_id : ''}}" >
    {!! $errors->first('service_id', '<p class="help-block">:message</p>') !!}
</div>
    
</div>

</div>

<div class="form-group">
    <input class="btn btn-primary" type="submit" value="{{ $formMode === 'edit' ? 'Update' : 'Create' }}">
</div>
