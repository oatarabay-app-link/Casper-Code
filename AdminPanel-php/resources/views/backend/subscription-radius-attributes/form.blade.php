<div class="form-group {{ $errors->has('subscription_id') ? 'has-error' : ''}}">
    <label for="subscription_id" class="control-label">{{ 'Subscription' }}</label>



    {!! $errors->first('subscription_id', '<p class="help-block">:message</p>') !!}

    {{  $subscription_id = isset($subscriptionradiusattribute->subscription_id) ? $subscriptionradiusattribute->subscription_id : ''}}

    <select class="form-control" name="subscription_id" type="number" id="subscription_id">

        @foreach($subs as $sub)
            <option value="{{ $sub->id }}" @if( $subscription_id==$sub->id ) selected="selected" @endif> {{ $sub->subscription_name }} </option>
        @endforeach
    </select>

    {!! $errors->first('subscription_id', '<p class="help-block">:message</p>') !!}



</div>
<div class="form-group {{ $errors->has('attribute') ? 'has-error' : ''}}">
    <label for="attribute" class="control-label">{{ 'Attribute' }}</label>
    <input class="form-control" name="attribute" type="text" id="attribute" value="{{ isset($subscriptionradiusattribute->attribute) ? $subscriptionradiusattribute->attribute : ''}}" >
    {!! $errors->first('attribute', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('op') ? 'has-error' : ''}}">
    <label for="op" class="control-label">{{ 'Operator' }}</label>
    <input class="form-control" name="op" type="text" id="op" value="{{ isset($subscriptionradiusattribute->op) ? $subscriptionradiusattribute->op : ''}}" >
    {!! $errors->first('op', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('value') ? 'has-error' : ''}}">
    <label for="value" class="control-label">{{ 'Value' }}</label>
    <input class="form-control" name="value" type="text" id="value" value="{{ isset($subscriptionradiusattribute->value) ? $subscriptionradiusattribute->value : ''}}" >
    {!! $errors->first('value', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('description') ? 'has-error' : ''}}">
    <label for="description" class="control-label">{{ 'Description' }}</label>
    <textarea class="form-control" rows="5" name="description" type="textarea" id="description" >{{ isset($subscriptionradiusattribute->description) ? $subscriptionradiusattribute->description : ''}}</textarea>
    {!! $errors->first('description', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('status') ? 'has-error' : ''}}">
    <label for="status" class="control-label">{{ 'Status' }}</label>
    <div class="radio">
    <label><input name="status" type="radio" value="1" {{ (isset($subscriptionradiusattribute) && 1 == $subscriptionradiusattribute->status) ? 'checked' : '' }}> Enable</label>
</div>
<div class="radio">
    <label><input name="status" type="radio" value="0" @if (isset($subscriptionradiusattribute)) {{ (0 == $subscriptionradiusattribute->status) ? 'checked' : '' }} @else {{ 'checked' }} @endif> Disable</label>
</div>
    {!! $errors->first('status', '<p class="help-block">:message</p>') !!}
</div>


<div class="form-group">
    <input class="btn btn-primary" type="submit" value="{{ $formMode === 'edit' ? 'Update' : 'Create' }}">
</div>
