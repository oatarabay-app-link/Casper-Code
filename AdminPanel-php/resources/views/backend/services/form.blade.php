<div class="row">

    <div class="col-sm-6">
    
    <div class="form-group {{ $errors->has('name') ? 'has-error' : ''}}">
    <label for="name" class="control-label">{{ 'Name' }}</label>
    <input class="form-control" name="name" type="text" id="name" value="{{ isset($service->name) ? $service->name : ''}}" >
    {!! $errors->first('name', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('type') ? 'has-error' : ''}}">
    <label for="type" class="control-label">{{ 'Type' }}</label>
    <input class="form-control" name="type" type="text" id="type" value="{{ isset($service->type) ? $service->type : ''}}" >
    {!! $errors->first('type', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('amount') ? 'has-error' : ''}}">
    <label for="amount" class="control-label">{{ 'Amount' }}</label>
    <input class="form-control" name="amount" type="number" id="amount" value="{{ isset($service->amount) ? $service->amount : ''}}" >
    {!! $errors->first('amount', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('is_recurring') ? 'has-error' : ''}}">
    <label for="is_recurring" class="control-label">{{ 'Is Recurring' }}</label>
    <div class="radio">
    <label><input name="is_recurring" type="radio" value="1" {{ (isset($service) && 1 == $service->is_recurring) ? 'checked' : '' }}> Yes</label>
</div>
<div class="radio">
    <label><input name="is_recurring" type="radio" value="0" @if (isset($service)) {{ (0 == $service->is_recurring) ? 'checked' : '' }} @else {{ 'checked' }} @endif> No</label>
</div>
    {!! $errors->first('is_recurring', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('is_autobill') ? 'has-error' : ''}}">
    <label for="is_autobill" class="control-label">{{ 'Is Autobill' }}</label>
    <div class="radio">
    <label><input name="is_autobill" type="radio" value="1" {{ (isset($service) && 1 == $service->is_autobill) ? 'checked' : '' }}> Yes</label>
</div>
<div class="radio">
    <label><input name="is_autobill" type="radio" value="0" @if (isset($service)) {{ (0 == $service->is_autobill) ? 'checked' : '' }} @else {{ 'checked' }} @endif> No</label>
</div>
    {!! $errors->first('is_autobill', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('setup_fee') ? 'has-error' : ''}}">
    <label for="setup_fee" class="control-label">{{ 'Setup Fee' }}</label>
    <input class="form-control" name="setup_fee" type="number" id="setup_fee" value="{{ isset($service->setup_fee) ? $service->setup_fee : ''}}" >
    {!! $errors->first('setup_fee', '<p class="help-block">:message</p>') !!}
</div>


</div>

<div class="col-sm-6">
    
    <div class="form-group {{ $errors->has('purchase_date') ? 'has-error' : ''}}">
    <label for="purchase_date" class="control-label">{{ 'Purchase Date' }}</label>
    <input class="form-control" name="purchase_date" type="date" id="purchase_date" value="{{ isset($service->purchase_date) ? $service->purchase_date : ''}}" >
    {!! $errors->first('purchase_date', '<p class="help-block">:message</p>') !!}
</div>


<div class="form-group {{ $errors->has('renewal_date') ? 'has-error' : ''}}">
    <label for="renewal_date" class="control-label">{{ 'Renewal Date' }}</label>
    <input class="form-control" name="renewal_date" type="date" id="renewal_date" value="{{ isset($service->renewal_date) ? $service->renewal_date : ''}}" >
    {!! $errors->first('renewal_date', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('notify') ? 'has-error' : ''}}">
    <label for="notify" class="control-label">{{ 'Notify' }}</label>
    <div class="radio">
    <label><input name="notify" type="radio" value="1" {{ (isset($service) && 1 == $service->notify) ? 'checked' : '' }}> Yes</label>
</div>
<div class="radio">
    <label><input name="notify" type="radio" value="0" @if (isset($service)) {{ (0 == $service->notify) ? 'checked' : '' }} @else {{ 'checked' }} @endif> No</label>
</div>
    {!! $errors->first('notify', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('notify_days') ? 'has-error' : ''}}">
    <label for="notify_days" class="control-label">{{ 'Notify Days' }}</label>
    <input class="form-control" name="notify_days" type="number" id="notify_days" value="{{ isset($service->notify_days) ? $service->notify_days : ''}}" >
    {!! $errors->first('notify_days', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('service_provider_id') ? 'has-error' : ''}}">
    <label for="service_provider_id" class="control-label">{{ 'Service Provider Id' }}</label>
    <input class="form-control" name="service_provider_id" type="number" id="service_provider_id" value="{{ isset($service->service_provider_id) ? $service->service_provider_id : ''}}" >
    {!! $errors->first('service_provider_id', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('notes') ? 'has-error' : ''}}">
    <label for="notes" class="control-label">{{ 'Notes' }}</label>
    <textarea class="form-control" rows="5" name="notes" type="textarea" id="notes" >{{ isset($service->notes) ? $service->notes : ''}}</textarea>
    {!! $errors->first('notes', '<p class="help-block">:message</p>') !!}
</div>

</div>

</div>


<div class="form-group">
    <input class="btn btn-primary" type="submit" value="{{ $formMode === 'edit' ? 'Update' : 'Create' }}">
</div>
