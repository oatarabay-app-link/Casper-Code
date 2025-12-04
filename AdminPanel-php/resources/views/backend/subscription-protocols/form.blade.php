<div class="form-group {{ $errors->has('subscription_uuid') ? 'has-error' : ''}}">
    <label for="subscription_uuid" class="control-label">{{ 'Subscription Uuid' }}</label>
    <input class="form-control" name="subscription_uuid" type="text" id="subscription_uuid" value="{{ isset($subscriptionprotocol->subscription_uuid) ? $subscriptionprotocol->subscription_uuid : ''}}" >
    {!! $errors->first('subscription_uuid', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('protocol_uuid') ? 'has-error' : ''}}">
    <label for="protocol_uuid" class="control-label">{{ 'Protocol Uuid' }}</label>
    <input class="form-control" name="protocol_uuid" type="text" id="protocol_uuid" value="{{ isset($subscriptionprotocol->protocol_uuid) ? $subscriptionprotocol->protocol_uuid : ''}}" >
    {!! $errors->first('protocol_uuid', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('protocol_id') ? 'has-error' : ''}}">
    <label for="protocol_id" class="control-label">{{ 'Protocol Id' }}</label>
    <input class="form-control" name="protocol_id" type="number" id="protocol_id" value="{{ isset($subscriptionprotocol->protocol_id) ? $subscriptionprotocol->protocol_id : ''}}" >
    {!! $errors->first('protocol_id', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('subscription_id') ? 'has-error' : ''}}">
    <label for="subscription_id" class="control-label">{{ 'Subscription Id' }}</label>
    <input class="form-control" name="subscription_id" type="number" id="subscription_id" value="{{ isset($subscriptionprotocol->subscription_id) ? $subscriptionprotocol->subscription_id : ''}}" >
    {!! $errors->first('subscription_id', '<p class="help-block">:message</p>') !!}
</div>


<div class="form-group">
    <input class="btn btn-primary" type="submit" value="{{ $formMode === 'edit' ? 'Update' : 'Create' }}">
</div>
