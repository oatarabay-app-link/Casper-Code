<div class="form-group {{ $errors->has('user_id') ? 'has-error' : ''}}">
    <label for="user_id" class="control-label">{{ 'User Id' }}</label>
    <input class="form-control" name="user_id" type="number" id="user_id" value="{{ isset($usersubscriptionextension->user_id) ? $usersubscriptionextension->user_id : ''}}" >
    {!! $errors->first('user_id', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('subscription_id') ? 'has-error' : ''}}">
    <label for="subscription_id" class="control-label">{{ 'Subscription Id' }}</label>
    <input class="form-control" name="subscription_id" type="number" id="subscription_id" value="{{ isset($usersubscriptionextension->subscription_id) ? $usersubscriptionextension->subscription_id : ''}}" >
    {!! $errors->first('subscription_id', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('days') ? 'has-error' : ''}}">
    <label for="days" class="control-label">{{ 'Days' }}</label>
    <input class="form-control" name="days" type="number" id="days" value="{{ isset($usersubscriptionextension->days) ? $usersubscriptionextension->days : ''}}" >
    {!! $errors->first('days', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('exipry_date') ? 'has-error' : ''}}">
    <label for="exipry_date" class="control-label">{{ 'Exipry Date' }}</label>
    <input class="form-control" name="exipry_date" type="datetime-local" id="exipry_date" value="{{ isset($usersubscriptionextension->exipry_date) ? $usersubscriptionextension->exipry_date : ''}}" >
    {!! $errors->first('exipry_date', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('note') ? 'has-error' : ''}}">
    <label for="note" class="control-label">{{ 'Note' }}</label>
    <textarea class="form-control" rows="5" name="note" type="textarea" id="note" >{{ isset($usersubscriptionextension->note) ? $usersubscriptionextension->note : ''}}</textarea>
    {!! $errors->first('note', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('added_by') ? 'has-error' : ''}}">
    <label for="added_by" class="control-label">{{ 'Added By' }}</label>
    <input class="form-control" name="added_by" type="number" id="added_by" value="{{ isset($usersubscriptionextension->added_by) ? $usersubscriptionextension->added_by : ''}}" >
    {!! $errors->first('added_by', '<p class="help-block">:message</p>') !!}
</div>


<div class="form-group">
    <input class="btn btn-primary" type="submit" value="{{ $formMode === 'edit' ? 'Update' : 'Create' }}">
</div>
