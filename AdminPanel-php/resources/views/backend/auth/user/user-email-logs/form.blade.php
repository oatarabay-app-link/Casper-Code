<div class="form-group {{ $errors->has('subject') ? 'has-error' : ''}}">
    <label for="subject" class="control-label">{{ 'Subject' }}</label>
    <input class="form-control" name="subject" type="text" id="subject" value="{{ isset($smtp2goemaildatum->subject) ? $smtp2goemaildatum->subject : ''}}" >
    {!! $errors->first('subject', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('delivered_at') ? 'has-error' : ''}}">
    <label for="delivered_at" class="control-label">{{ 'Delivered At' }}</label>
    <input class="form-control" name="delivered_at" type="text" id="delivered_at" value="{{ isset($smtp2goemaildatum->delivered_at) ? $smtp2goemaildatum->delivered_at : ''}}" >
    {!! $errors->first('delivered_at', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('process_status') ? 'has-error' : ''}}">
    <label for="process_status" class="control-label">{{ 'Process Status' }}</label>
    <input class="form-control" name="process_status" type="text" id="process_status" value="{{ isset($smtp2goemaildatum->process_status) ? $smtp2goemaildatum->process_status : ''}}" >
    {!! $errors->first('process_status', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('email_id') ? 'has-error' : ''}}">
    <label for="email_id" class="control-label">{{ 'Email Id' }}</label>
    <input class="form-control" name="email_id" type="text" id="email_id" value="{{ isset($smtp2goemaildatum->email_id) ? $smtp2goemaildatum->email_id : ''}}" >
    {!! $errors->first('email_id', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('status') ? 'has-error' : ''}}">
    <label for="status" class="control-label">{{ 'Status' }}</label>
    <input class="form-control" name="status" type="text" id="status" value="{{ isset($smtp2goemaildatum->status) ? $smtp2goemaildatum->status : ''}}" >
    {!! $errors->first('status', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('response') ? 'has-error' : ''}}">
    <label for="response" class="control-label">{{ 'Response' }}</label>
    <input class="form-control" name="response" type="text" id="response" value="{{ isset($smtp2goemaildatum->response) ? $smtp2goemaildatum->response : ''}}" >
    {!! $errors->first('response', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('email_tx') ? 'has-error' : ''}}">
    <label for="email_tx" class="control-label">{{ 'Email Tx' }}</label>
    <input class="form-control" name="email_tx" type="text" id="email_tx" value="{{ isset($smtp2goemaildatum->email_tx) ? $smtp2goemaildatum->email_tx : ''}}" >
    {!! $errors->first('email_tx', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('host') ? 'has-error' : ''}}">
    <label for="host" class="control-label">{{ 'Host' }}</label>
    <input class="form-control" name="host" type="text" id="host" value="{{ isset($smtp2goemaildatum->host) ? $smtp2goemaildatum->host : ''}}" >
    {!! $errors->first('host', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('smtpcode') ? 'has-error' : ''}}">
    <label for="smtpcode" class="control-label">{{ 'Smtpcode' }}</label>
    <input class="form-control" name="smtpcode" type="text" id="smtpcode" value="{{ isset($smtp2goemaildatum->smtpcode) ? $smtp2goemaildatum->smtpcode : ''}}" >
    {!! $errors->first('smtpcode', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('sender') ? 'has-error' : ''}}">
    <label for="sender" class="control-label">{{ 'Sender' }}</label>
    <input class="form-control" name="sender" type="text" id="sender" value="{{ isset($smtp2goemaildatum->sender) ? $smtp2goemaildatum->sender : ''}}" >
    {!! $errors->first('sender', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('recipient') ? 'has-error' : ''}}">
    <label for="recipient" class="control-label">{{ 'Recipient' }}</label>
    <input class="form-control" name="recipient" type="text" id="recipient" value="{{ isset($smtp2goemaildatum->recipient) ? $smtp2goemaildatum->recipient : ''}}" >
    {!! $errors->first('recipient', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('stmp2gousername') ? 'has-error' : ''}}">
    <label for="stmp2gousername" class="control-label">{{ 'Stmp2gousername' }}</label>
    <input class="form-control" name="stmp2gousername" type="text" id="stmp2gousername" value="{{ isset($smtp2goemaildatum->stmp2gousername) ? $smtp2goemaildatum->stmp2gousername : ''}}" >
    {!! $errors->first('stmp2gousername', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('headers') ? 'has-error' : ''}}">
    <label for="headers" class="control-label">{{ 'Headers' }}</label>
    <textarea class="form-control" rows="5" name="headers" type="textarea" id="headers" >{{ isset($smtp2goemaildatum->headers) ? $smtp2goemaildatum->headers : ''}}</textarea>
    {!! $errors->first('headers', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('total_opens') ? 'has-error' : ''}}">
    <label for="total_opens" class="control-label">{{ 'Total Opens' }}</label>
    <input class="form-control" name="total_opens" type="text" id="total_opens" value="{{ isset($smtp2goemaildatum->total_opens) ? $smtp2goemaildatum->total_opens : ''}}" >
    {!! $errors->first('total_opens', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('opens') ? 'has-error' : ''}}">
    <label for="opens" class="control-label">{{ 'Opens' }}</label>
    <textarea class="form-control" rows="5" name="opens" type="textarea" id="opens" >{{ isset($smtp2goemaildatum->opens) ? $smtp2goemaildatum->opens : ''}}</textarea>
    {!! $errors->first('opens', '<p class="help-block">:message</p>') !!}
</div>


<div class="form-group">
    <input class="btn btn-primary" type="submit" value="{{ $formMode === 'edit' ? 'Update' : 'Create' }}">
</div>
