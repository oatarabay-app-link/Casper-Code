<div class="row">
    <div class="col-sm-6">

<div class="form-group {{ $errors->has('uuid') ? 'has-error' : ''}}">
    <label for="uuid" class="control-label">{{ 'Uuid' }}</label>
    <input class="form-control" name="uuid" type="text" id="uuid" value="{{ isset($serviceprovider->uuid) ? $serviceprovider->uuid : ''}}" >
    {!! $errors->first('uuid', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('name') ? 'has-error' : ''}}">
    <label for="name" class="control-label">{{ 'Name' }}</label>
    <input class="form-control" name="name" type="text" id="name" value="{{ isset($serviceprovider->name) ? $serviceprovider->name : ''}}" >
    {!! $errors->first('name', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('url') ? 'has-error' : ''}}">
    <label for="url" class="control-label">{{ 'Url' }}</label>
    <input class="form-control" name="url" type="text" id="url" value="{{ isset($serviceprovider->url) ? $serviceprovider->url : ''}}" >
    {!! $errors->first('url', '<p class="help-block">:message</p>') !!}
</div>


</div>

<div class="col-sm-6">
<div class="form-group {{ $errors->has('username') ? 'has-error' : ''}}">
    <label for="username" class="control-label">{{ 'Username' }}</label>
    <input class="form-control" name="username" type="text" id="username" value="{{ isset($serviceprovider->username) ? $serviceprovider->username : ''}}" >
    {!! $errors->first('username', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('password') ? 'has-error' : ''}}">
    <label for="password" class="control-label">{{ 'Password' }}</label>
    <input class="form-control" name="password" type="text" id="password" value="{{ isset($serviceprovider->password) ? $serviceprovider->password : ''}}" >
    {!! $errors->first('password', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('provide_type') ? 'has-error' : ''}}">
    <label for="provider_type" class="control-label">{{ 'Provider Type' }}</label>
    <input class="form-control" name="provider_type" type="text" id="provider_type" value="{{ isset($serviceprovider->provider_type) ? $serviceprovider->provider_type : ''}}" >
    {!! $errors->first('provide_type', '<p class="help-block">:message</p>') !!}
</div>
    

</div>

</div>

<div class="form-group">
    <input class="btn btn-primary" type="submit" value="{{ $formMode === 'edit' ? 'Update' : 'Create' }}">
</div>
