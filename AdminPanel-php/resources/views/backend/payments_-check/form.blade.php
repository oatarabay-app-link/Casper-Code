<div class="form-group {{ $errors->has('uuid') ? 'has-error' : ''}}">
    <label for="uuid" class="control-label">{{ 'Uuid' }}</label>
    <input class="form-control" name="uuid" type="text" id="uuid" value="{{ isset($payments_check->uuid) ? $payments_check->uuid : ''}}" >
    {!! $errors->first('uuid', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('create_date') ? 'has-error' : ''}}">
    <label for="create_date" class="control-label">{{ 'Create Date' }}</label>
    <input class="form-control" name="create_date" type="datetime-local" id="create_date" value="{{ isset($payments_check->create_date) ? $payments_check->create_date : ''}}" >
    {!! $errors->first('create_date', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('subscription_uuid') ? 'has-error' : ''}}">
    <label for="subscription_uuid" class="control-label">{{ 'Subscription Uuid' }}</label>
    <input class="form-control" name="subscription_uuid" type="text" id="subscription_uuid" value="{{ isset($payments_check->subscription_uuid) ? $payments_check->subscription_uuid : ''}}" >
    {!! $errors->first('subscription_uuid', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('user_uuid') ? 'has-error' : ''}}">
    <label for="user_uuid" class="control-label">{{ 'User Uuid' }}</label>
    <input class="form-control" name="user_uuid" type="text" id="user_uuid" value="{{ isset($payments_check->user_uuid) ? $payments_check->user_uuid : ''}}" >
    {!! $errors->first('user_uuid', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('user_id') ? 'has-error' : ''}}">
    <label for="user_id" class="control-label">{{ 'User Id' }}</label>
    <input class="form-control" name="user_id" type="number" id="user_id" value="{{ isset($payments_check->user_id) ? $payments_check->user_id : ''}}" >
    {!! $errors->first('user_id', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('user_email') ? 'has-error' : ''}}">
    <label for="user_email" class="control-label">{{ 'User Email' }}</label>
    <input class="form-control" name="user_email" type="text" id="user_email" value="{{ isset($payments_check->user_email) ? $payments_check->user_email : ''}}" >
    {!! $errors->first('user_email', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('subscription_id') ? 'has-error' : ''}}">
    <label for="subscription_id" class="control-label">{{ 'Subscription Id' }}</label>
    <input class="form-control" name="subscription_id" type="number" id="subscription_id" value="{{ isset($payments_check->subscription_id) ? $payments_check->subscription_id : ''}}" >
    {!! $errors->first('subscription_id', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('token') ? 'has-error' : ''}}">
    <label for="token" class="control-label">{{ 'Token' }}</label>
    <textarea class="form-control" rows="5" name="token" type="textarea" id="token" >{{ isset($payments_check->token) ? $payments_check->token : ''}}</textarea>
    {!! $errors->first('token', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('status') ? 'has-error' : ''}}">
    <label for="status" class="control-label">{{ 'Status' }}</label>
    <textarea class="form-control" rows="5" name="status" type="textarea" id="status" >{{ isset($payments_check->status) ? $payments_check->status : ''}}</textarea>
    {!! $errors->first('status', '<p class="help-block">:message</p>') !!}
</div>


<div class="form-group">
    <input class="btn btn-primary" type="submit" value="{{ $formMode === 'edit' ? 'Update' : 'Create' }}">
</div>
