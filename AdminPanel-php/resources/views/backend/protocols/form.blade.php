<div class="row">
	<div class="col-sm-6">
<div class="form-group {{ $errors->has('uuid') ? 'has-error' : ''}}">
    <label for="uuid" class="control-label">{{ 'Uuid' }}</label>
    <input class="form-control" name="uuid" type="text" id="uuid" value="{{ isset($protocol->uuid) ? $protocol->uuid : ''}}" >
    {!! $errors->first('uuid', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('title') ? 'has-error' : ''}}">
    <label for="title" class="control-label">{{ 'Name' }}</label>
    <input class="form-control" name="title" type="text" id="title" value="{{ isset($protocol->title) ? $protocol->title : ''}}" >
    {!! $errors->first('title', '<p class="help-block">:message</p>') !!}
</div>

</div></div>
<div class="form-group">
    <input class="btn btn-primary" type="submit" value="{{ $formMode === 'edit' ? 'Update' : 'Create' }}">
</div>
