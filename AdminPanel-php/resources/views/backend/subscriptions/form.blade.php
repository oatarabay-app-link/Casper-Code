<div class="row">

    <div class="col-sm-6">


<div class="form-group {{ $errors->has('uuid') ? 'has-error' : ''}}">
    <label for="uuid" class="control-label">{{ 'Uuid' }}</label>
    <input class="form-control" name="uuid" type="text" id="uuid" value="{{ isset($subscription->uuid) ? $subscription->uuid : ''}}" >
    {!! $errors->first('uuid', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('subscription_name') ? 'has-error' : ''}}">
    <label for="subscription_name" class="control-label">{{ 'Subscription Name' }}</label>
    <input class="form-control" name="subscription_name" type="text" id="subscription_name" value="{{ isset($subscription->subscription_name) ? $subscription->subscription_name : ''}}" >
    {!! $errors->first('subscription_name', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('monthly_price') ? 'has-error' : ''}}">
    <label for="monthly_price" class="control-label">{{ 'Monthly Price' }}</label>
    <input class="form-control" name="monthly_price" type="number" id="monthly_price" value="{{ isset($subscription->monthly_price) ? $subscription->monthly_price : ''}}" >
    {!! $errors->first('monthly_price', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('period_price') ? 'has-error' : ''}}">
    <label for="period_price" class="control-label">{{ 'Period Price' }}</label>
    <input class="form-control" name="period_price" type="number" id="period_price" value="{{ isset($subscription->period_price) ? $subscription->period_price : ''}}" >
    {!! $errors->first('period_price', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('currency_type') ? 'has-error' : ''}}">
    <label for="currency_type" class="control-label">{{ 'Currency Type' }}</label>
    <input class="form-control" name="currency_type" type="text" id="currency_type" value="{{ isset($subscription->currency_type) ? $subscription->currency_type : ''}}" >
    {!! $errors->first('currency_type', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('traffic_size') ? 'has-error' : ''}}">
    <label for="traffic_size" class="control-label">{{ 'Traffic Size' }}</label>
    <input class="form-control" name="traffic_size" type="number" id="traffic_size" value="{{ isset($subscription->traffic_size) ? $subscription->traffic_size : ''}}" >
    {!! $errors->first('traffic_size', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('rate_limit') ? 'has-error' : ''}}">
    <label for="rate_limit" class="control-label">{{ 'Rate Limit' }}</label>
    <input class="form-control" name="rate_limit" type="number" id="rate_limit" value="{{ isset($subscription->rate_limit) ? $subscription->rate_limit : ''}}" >
    {!! $errors->first('rate_limit', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('max_connections') ? 'has-error' : ''}}">
    <label for="max_connections" class="control-label">{{ 'Max Connections' }}</label>
    <input class="form-control" name="max_connections" type="number" id="max_connections" value="{{ isset($subscription->max_connections) ? $subscription->max_connections : ''}}" >
    {!! $errors->first('max_connections', '<p class="help-block">:message</p>') !!}
</div>


</div>


<div class="col-sm-6">
   

<div class="form-group {{ $errors->has('available_for_android') ? 'has-error' : ''}}">
    <label for="available_for_android" class="control-label">{{ 'Available For Android' }}</label>
    <div class="radio">
    <label><input name="available_for_android" type="radio" value="1" {{ (isset($subscription) && 1 == $subscription->available_for_android) ? 'checked' : '' }}> Yes</label>
</div>
<div class="radio">
    <label><input name="available_for_android" type="radio" value="0" @if (isset($subscription)) {{ (0 == $subscription->available_for_android) ? 'checked' : '' }} @else {{ 'checked' }} @endif> No</label>
</div>
    {!! $errors->first('available_for_android', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('available_for_ios') ? 'has-error' : ''}}">
    <label for="available_for_ios" class="control-label">{{ 'Available For Ios' }}</label>
    <div class="radio">
    <label><input name="available_for_ios" type="radio" value="1" {{ (isset($subscription) && 1 == $subscription->available_for_ios) ? 'checked' : '' }}> Yes</label>
</div>
<div class="radio">
    <label><input name="available_for_ios" type="radio" value="0" @if (isset($subscription)) {{ (0 == $subscription->available_for_ios) ? 'checked' : '' }} @else {{ 'checked' }} @endif> No</label>
</div>
    {!! $errors->first('available_for_ios', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('create_time') ? 'has-error' : ''}}">
    <label for="create_time" class="control-label">{{ 'Create Time' }}</label>
    <input class="form-control" name="create_time" type="datetime-local" id="create_time" value="{{ isset($subscription->create_time) ? $subscription->create_time : ''}}" >
    {!! $errors->first('create_time', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('is_default') ? 'has-error' : ''}}">
    <label for="is_default" class="control-label">{{ 'Is Default' }}</label>
    <div class="radio">
    <label><input name="is_default" type="radio" value="1" {{ (isset($subscription) && 1 == $subscription->is_default) ? 'checked' : '' }}> Yes</label>
</div>
<div class="radio">
    <label><input name="is_default" type="radio" value="0" @if (isset($subscription)) {{ (0 == $subscription->is_default) ? 'checked' : '' }} @else {{ 'checked' }} @endif> No</label>
</div>
    {!! $errors->first('is_default', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('period_length') ? 'has-error' : ''}}">
    <label for="period_length" class="control-label">{{ 'Period Length' }}</label>
    <input class="form-control" name="period_length" type="number" id="period_length" value="{{ isset($subscription->period_length) ? $subscription->period_length : ''}}" >
    {!! $errors->first('period_length', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('order_num') ? 'has-error' : ''}}">
    <label for="order_num" class="control-label">{{ 'Order Num' }}</label>
    <input class="form-control" name="order_num" type="number" id="order_num" value="{{ isset($subscription->order_num) ? $subscription->order_num : ''}}" >
    {!! $errors->first('order_num', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('product_id') ? 'has-error' : ''}}">
    <label for="product_id" class="control-label">{{ 'Product Id' }}</label>
    <input class="form-control" name="product_id" type="number" id="product_id" value="{{ isset($subscription->product_id) ? $subscription->product_id : ''}}" >
    {!! $errors->first('product_id', '<p class="help-block">:message</p>') !!}
</div>
  

</div>

</div>



<div class="form-group">
    <input class="btn btn-primary" type="submit" value="{{ $formMode === 'edit' ? 'Update' : 'Create' }}">
</div>
