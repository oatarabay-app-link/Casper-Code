<div class="form-group {{ $errors->has('uuid') ? 'has-error' : ''}}">
    <label for="uuid" class="control-label">{{ 'Uuid' }}</label>
    <input class="form-control" name="uuid" type="text" id="uuid" value="{{ isset($usersubscription->uuid) ? $usersubscription->uuid : ''}}" >
    {!! $errors->first('uuid', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('subscription_uuid') ? 'has-error' : ''}}">
    <label for="subscription_uuid" class="control-label">{{ 'Subscription Uuid' }}</label>
    <input class="form-control" name="subscription_uuid" type="text" id="subscription_uuid" value="{{ isset($usersubscription->subscription_uuid) ? $usersubscription->subscription_uuid : ''}}" >
    {!! $errors->first('subscription_uuid', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('subscription_start_date') ? 'has-error' : ''}}">
    <label for="subscription_start_date" class="control-label">{{ 'Subscription Start Date' }}</label>
    <input class="form-control" name="subscription_start_date" type="datetime-local" id="subscription_start_date" value="{{ isset($usersubscription->subscription_start_date) ? $usersubscription->subscription_start_date : ''}}" >
    {!! $errors->first('subscription_start_date', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('subscription_end_date') ? 'has-error' : ''}}">
    <label for="subscription_end_date" class="control-label">{{ 'Subscription End Date' }}</label>
    <input class="form-control" name="subscription_end_date" type="datetime-local" id="subscription_end_date" value="{{ isset($usersubscription->subscription_end_date) ? $usersubscription->subscription_end_date : ''}}" >
    {!! $errors->first('subscription_end_date', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('vpn_pass') ? 'has-error' : ''}}">
    <label for="vpn_pass" class="control-label">{{ 'Vpn Pass' }}</label>
    <input class="form-control" name="vpn_pass" type="text" id="vpn_pass" value="{{ isset($usersubscription->vpn_pass) ? $usersubscription->vpn_pass : ''}}" >
    {!! $errors->first('vpn_pass', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('is_active') ? 'has-error' : ''}}">
    <label for="is_active" class="control-label">{{ 'Is Active' }}</label>
    <div class="radio">
    <label><input name="is_active" type="radio" value="1" {{ (isset($usersubscription) && 1 == $usersubscription->is_active) ? 'checked' : '' }}> Yes</label>
</div>
<div class="radio">
    <label><input name="is_active" type="radio" value="0" @if (isset($usersubscription)) {{ (0 == $usersubscription->is_active) ? 'checked' : '' }} @else {{ 'checked' }} @endif> No</label>
</div>
    {!! $errors->first('is_active', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('subscription_id') ? 'has-error' : ''}}">
    <label for="subscription_id" class="control-label">{{ 'Subscription Id' }}</label>
    <input class="form-control" name="subscription_id" type="number" id="subscription_id" value="{{ isset($usersubscription->subscription_id) ? $usersubscription->subscription_id : ''}}" >
    {!! $errors->first('subscription_id', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('user_id') ? 'has-error' : ''}}">
    <label for="user_id" class="control-label">{{ 'User Id' }}</label>
    <input class="form-control" name="user_id" type="number" id="user_id" value="{{ isset($usersubscription->user_id) ? $usersubscription->user_id : ''}}" >
    {!! $errors->first('user_id', '<p class="help-block">:message</p>') !!}
</div>


<div class="form-group">
    <input class="btn btn-primary" type="submit" value="{{ $formMode === 'edit' ? 'Update' : 'Create' }}">
</div>
