<div class="form-group {{ $errors->has('vpnserver_uuid') ? 'has-error' : ''}}">
    <label for="vpnserver_uuid" class="control-label">{{ 'Vpnserver Uuid' }}</label>
    <input class="form-control" name="vpnserver_uuid" type="text" id="vpnserver_uuid" value="{{ isset($vpnserverprotocol->vpnserver_uuid) ? $vpnserverprotocol->vpnserver_uuid : ''}}" >
    {!! $errors->first('vpnserver_uuid', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('protocol_uuid') ? 'has-error' : ''}}">
    <label for="protocol_uuid" class="control-label">{{ 'Protocol Uuid' }}</label>
    <input class="form-control" name="protocol_uuid" type="text" id="protocol_uuid" value="{{ isset($vpnserverprotocol->protocol_uuid) ? $vpnserverprotocol->protocol_uuid : ''}}" >
    {!! $errors->first('protocol_uuid', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('vpnserver_id') ? 'has-error' : ''}}">
    <label for="vpnserver_id" class="control-label">{{ 'Vpnserver Id' }}</label>
    <input class="form-control" name="vpnserver_id" type="number" id="vpnserver_id" value="{{ isset($vpnserverprotocol->vpnserver_id) ? $vpnserverprotocol->vpnserver_id : ''}}" >
    {!! $errors->first('vpnserver_id', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('protocol_id') ? 'has-error' : ''}}">
    <label for="protocol_id" class="control-label">{{ 'Protocol Id' }}</label>
    <input class="form-control" name="protocol_id" type="number" id="protocol_id" value="{{ isset($vpnserverprotocol->protocol_id) ? $vpnserverprotocol->protocol_id : ''}}" >
    {!! $errors->first('protocol_id', '<p class="help-block">:message</p>') !!}
</div>


<div class="form-group">
    <input class="btn btn-primary" type="submit" value="{{ $formMode === 'edit' ? 'Update' : 'Create' }}">
</div>
