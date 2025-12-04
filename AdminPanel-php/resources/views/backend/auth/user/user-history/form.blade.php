<div class="form-group {{ $errors->has('user_id') ? 'has-error' : ''}}">
    <label for="user_id" class="control-label">{{ 'User Id' }}</label>
    <input class="form-control" name="user_id" type="number" id="user_id" value="{{ isset($userhistory->user_id) ? $userhistory->user_id : ''}}" >
    {!! $errors->first('user_id', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('event') ? 'has-error' : ''}}">
    <label for="event" class="control-label">{{ 'Event' }}</label>
    <input class="form-control" name="event" type="text" id="event" value="{{ isset($userhistory->event) ? $userhistory->event : ''}}" >
    {!! $errors->first('event', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('operation') ? 'has-error' : ''}}">
    <label for="operation" class="control-label">{{ 'Operation' }}</label>
    <input class="form-control" name="operation" type="text" id="operation" value="{{ isset($userhistory->operation) ? $userhistory->operation : ''}}" >
    {!! $errors->first('operation', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('result') ? 'has-error' : ''}}">
    <label for="result" class="control-label">{{ 'Result' }}</label>
    <input class="form-control" name="result" type="text" id="result" value="{{ isset($userhistory->result) ? $userhistory->result : ''}}" >
    {!! $errors->first('result', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('description') ? 'has-error' : ''}}">
    <label for="description" class="control-label">{{ 'Description' }}</label>
    <textarea class="form-control" rows="5" name="description" type="textarea" id="description" >{{ isset($userhistory->description) ? $userhistory->description : ''}}</textarea>
    {!! $errors->first('description', '<p class="help-block">:message</p>') !!}
</div>


<div class="form-group">
    <input class="btn btn-primary" type="submit" value="{{ $formMode === 'edit' ? 'Update' : 'Create' }}">
</div>
