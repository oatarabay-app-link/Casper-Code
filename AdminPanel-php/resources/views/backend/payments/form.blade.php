<div class="form-group {{ $errors->has('uuid') ? 'has-error' : ''}}">
    <label for="uuid" class="control-label">{{ 'Uuid' }}</label>
    <input class="form-control" name="uuid" type="text" id="uuid" value="{{ isset($payment->uuid) ? $payment->uuid : ''}}" >
    {!! $errors->first('uuid', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('subscription_uuid') ? 'has-error' : ''}}">
    <label for="subscription_uuid" class="control-label">{{ 'Subscription Uuid' }}</label>
    <input class="form-control" name="subscription_uuid" type="text" id="subscription_uuid" value="{{ isset($payment->subscription_uuid) ? $payment->subscription_uuid : ''}}" >
    {!! $errors->first('subscription_uuid', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('subscription_id') ? 'has-error' : ''}}">
    <label for="subscription_id" class="control-label">{{ 'Subscription Id' }}</label>
    <input class="form-control" name="subscription_id" type="number" id="subscription_id" value="{{ isset($payment->subscription_id) ? $payment->subscription_id : ''}}" >
    {!! $errors->first('subscription_id', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('user_id') ? 'has-error' : ''}}">
    <label for="user_id" class="control-label">{{ 'User Id' }}</label>
    <input class="form-control" name="user_id" type="number" id="user_id" value="{{ isset($payment->user_id) ? $payment->user_id : ''}}" >
    {!! $errors->first('user_id', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('period_in_months') ? 'has-error' : ''}}">
    <label for="period_in_months" class="control-label">{{ 'Period In Months' }}</label>
    <input class="form-control" name="period_in_months" type="number" id="period_in_months" value="{{ isset($payment->period_in_months) ? $payment->period_in_months : ''}}" >
    {!! $errors->first('period_in_months', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('payment_id') ? 'has-error' : ''}}">
    <label for="payment_id" class="control-label">{{ 'Payment Id' }}</label>
    <input class="form-control" name="payment_id" type="text" id="payment_id" value="{{ isset($payment->payment_id) ? $payment->payment_id : ''}}" >
    {!! $errors->first('payment_id', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('status') ? 'has-error' : ''}}">
    <label for="status" class="control-label">{{ 'Status' }}</label>
    <input class="form-control" name="status" type="text" id="status" value="{{ isset($payment->status) ? $payment->status : ''}}" >
    {!! $errors->first('status', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('payment_sum') ? 'has-error' : ''}}">
    <label for="payment_sum" class="control-label">{{ 'Payment Sum' }}</label>
    <input class="form-control" name="payment_sum" type="number" id="payment_sum" value="{{ isset($payment->payment_sum) ? $payment->payment_sum : ''}}" >
    {!! $errors->first('payment_sum', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('details') ? 'has-error' : ''}}">
    <label for="details" class="control-label">{{ 'Details' }}</label>
    <textarea class="form-control" rows="5" name="details" type="textarea" id="details" >{{ isset($payment->details) ? $payment->details : ''}}</textarea>
    {!! $errors->first('details', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('check_code') ? 'has-error' : ''}}">
    <label for="check_code" class="control-label">{{ 'Check Code' }}</label>
    <textarea class="form-control" rows="5" name="check_code" type="textarea" id="check_code" >{{ isset($payment->check_code) ? $payment->check_code : ''}}</textarea>
    {!! $errors->first('check_code', '<p class="help-block">:message</p>') !!}
</div>


<div class="form-group">
    <input class="btn btn-primary" type="submit" value="{{ $formMode === 'edit' ? 'Update' : 'Create' }}">
</div>
