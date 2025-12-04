<div class="form-group {{ $errors->has('groupname') ? 'has-error' : ''}}">
    <label for="groupname" class="control-label">{{ 'Groupname' }}</label>
    <input class="form-control" name="groupname" type="text" id="groupname" value="{{ isset($radacct->groupname) ? $radacct->groupname : ''}}" >
    {!! $errors->first('groupname', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('realm') ? 'has-error' : ''}}">
    <label for="realm" class="control-label">{{ 'Realm' }}</label>
    <input class="form-control" name="realm" type="text" id="realm" value="{{ isset($radacct->realm) ? $radacct->realm : ''}}" >
    {!! $errors->first('realm', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('nasipaddress') ? 'has-error' : ''}}">
    <label for="nasipaddress" class="control-label">{{ 'Nasipaddress' }}</label>
    <input class="form-control" name="nasipaddress" type="text" id="nasipaddress" value="{{ isset($radacct->nasipaddress) ? $radacct->nasipaddress : ''}}" >
    {!! $errors->first('nasipaddress', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('nasidentifier') ? 'has-error' : ''}}">
    <label for="nasidentifier" class="control-label">{{ 'Nasidentifier' }}</label>
    <input class="form-control" name="nasidentifier" type="text" id="nasidentifier" value="{{ isset($radacct->nasidentifier) ? $radacct->nasidentifier : ''}}" >
    {!! $errors->first('nasidentifier', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('nasportid') ? 'has-error' : ''}}">
    <label for="nasportid" class="control-label">{{ 'Nasportid' }}</label>
    <input class="form-control" name="nasportid" type="text" id="nasportid" value="{{ isset($radacct->nasportid) ? $radacct->nasportid : ''}}" >
    {!! $errors->first('nasportid', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('nasporttype') ? 'has-error' : ''}}">
    <label for="nasporttype" class="control-label">{{ 'Nasporttype' }}</label>
    <input class="form-control" name="nasporttype" type="text" id="nasporttype" value="{{ isset($radacct->nasporttype) ? $radacct->nasporttype : ''}}" >
    {!! $errors->first('nasporttype', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('acctstarttime') ? 'has-error' : ''}}">
    <label for="acctstarttime" class="control-label">{{ 'Acctstarttime' }}</label>
    <input class="form-control" name="acctstarttime" type="datetime-local" id="acctstarttime" value="{{ isset($radacct->acctstarttime) ? $radacct->acctstarttime : ''}}" >
    {!! $errors->first('acctstarttime', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('acctstoptime') ? 'has-error' : ''}}">
    <label for="acctstoptime" class="control-label">{{ 'Acctstoptime' }}</label>
    <input class="form-control" name="acctstoptime" type="datetime-local" id="acctstoptime" value="{{ isset($radacct->acctstoptime) ? $radacct->acctstoptime : ''}}" >
    {!! $errors->first('acctstoptime', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('acctsesslontime') ? 'has-error' : ''}}">
    <label for="acctsesslontime" class="control-label">{{ 'Acctsesslontime' }}</label>
    <input class="form-control" name="acctsesslontime" type="number" id="acctsesslontime" value="{{ isset($radacct->acctsesslontime) ? $radacct->acctsesslontime : ''}}" >
    {!! $errors->first('acctsesslontime', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('acctauthentic') ? 'has-error' : ''}}">
    <label for="acctauthentic" class="control-label">{{ 'Acctauthentic' }}</label>
    <input class="form-control" name="acctauthentic" type="text" id="acctauthentic" value="{{ isset($radacct->acctauthentic) ? $radacct->acctauthentic : ''}}" >
    {!! $errors->first('acctauthentic', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('connectinfo_start') ? 'has-error' : ''}}">
    <label for="connectinfo_start" class="control-label">{{ 'Connectinfo Start' }}</label>
    <input class="form-control" name="connectinfo_start" type="text" id="connectinfo_start" value="{{ isset($radacct->connectinfo_start) ? $radacct->connectinfo_start : ''}}" >
    {!! $errors->first('connectinfo_start', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('connectinfo_stop') ? 'has-error' : ''}}">
    <label for="connectinfo_stop" class="control-label">{{ 'Connectinfo Stop' }}</label>
    <input class="form-control" name="connectinfo_stop" type="text" id="connectinfo_stop" value="{{ isset($radacct->connectinfo_stop) ? $radacct->connectinfo_stop : ''}}" >
    {!! $errors->first('connectinfo_stop', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('acctinputoctest') ? 'has-error' : ''}}">
    <label for="acctinputoctest" class="control-label">{{ 'Acctinputoctest' }}</label>
    <input class="form-control" name="acctinputoctest" type="number" id="acctinputoctest" value="{{ isset($radacct->acctinputoctest) ? $radacct->acctinputoctest : ''}}" >
    {!! $errors->first('acctinputoctest', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('acctoutputoctest') ? 'has-error' : ''}}">
    <label for="acctoutputoctest" class="control-label">{{ 'Acctoutputoctest' }}</label>
    <input class="form-control" name="acctoutputoctest" type="text" id="acctoutputoctest" value="{{ isset($radacct->acctoutputoctest) ? $radacct->acctoutputoctest : ''}}" >
    {!! $errors->first('acctoutputoctest', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('calledstationid') ? 'has-error' : ''}}">
    <label for="calledstationid" class="control-label">{{ 'Calledstationid' }}</label>
    <input class="form-control" name="calledstationid" type="text" id="calledstationid" value="{{ isset($radacct->calledstationid) ? $radacct->calledstationid : ''}}" >
    {!! $errors->first('calledstationid', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('callingstationid') ? 'has-error' : ''}}">
    <label for="callingstationid" class="control-label">{{ 'Callingstationid' }}</label>
    <input class="form-control" name="callingstationid" type="text" id="callingstationid" value="{{ isset($radacct->callingstationid) ? $radacct->callingstationid : ''}}" >
    {!! $errors->first('callingstationid', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('acctterminatecause') ? 'has-error' : ''}}">
    <label for="acctterminatecause" class="control-label">{{ 'Acctterminatecause' }}</label>
    <input class="form-control" name="acctterminatecause" type="text" id="acctterminatecause" value="{{ isset($radacct->acctterminatecause) ? $radacct->acctterminatecause : ''}}" >
    {!! $errors->first('acctterminatecause', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('servicetype') ? 'has-error' : ''}}">
    <label for="servicetype" class="control-label">{{ 'Servicetype' }}</label>
    <input class="form-control" name="servicetype" type="text" id="servicetype" value="{{ isset($radacct->servicetype) ? $radacct->servicetype : ''}}" >
    {!! $errors->first('servicetype', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('framedprotocol') ? 'has-error' : ''}}">
    <label for="framedprotocol" class="control-label">{{ 'Framedprotocol' }}</label>
    <input class="form-control" name="framedprotocol" type="text" id="framedprotocol" value="{{ isset($radacct->framedprotocol) ? $radacct->framedprotocol : ''}}" >
    {!! $errors->first('framedprotocol', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('framedipaddress') ? 'has-error' : ''}}">
    <label for="framedipaddress" class="control-label">{{ 'Framedipaddress' }}</label>
    <input class="form-control" name="framedipaddress" type="text" id="framedipaddress" value="{{ isset($radacct->framedipaddress) ? $radacct->framedipaddress : ''}}" >
    {!! $errors->first('framedipaddress', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('acctstartdelay') ? 'has-error' : ''}}">
    <label for="acctstartdelay" class="control-label">{{ 'Acctstartdelay' }}</label>
    <input class="form-control" name="acctstartdelay" type="number" id="acctstartdelay" value="{{ isset($radacct->acctstartdelay) ? $radacct->acctstartdelay : ''}}" >
    {!! $errors->first('acctstartdelay', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('acctstopdelay') ? 'has-error' : ''}}">
    <label for="acctstopdelay" class="control-label">{{ 'Acctstopdelay' }}</label>
    <input class="form-control" name="acctstopdelay" type="number" id="acctstopdelay" value="{{ isset($radacct->acctstopdelay) ? $radacct->acctstopdelay : ''}}" >
    {!! $errors->first('acctstopdelay', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('xascendsessionsvrkey') ? 'has-error' : ''}}">
    <label for="xascendsessionsvrkey" class="control-label">{{ 'Xascendsessionsvrkey' }}</label>
    <input class="form-control" name="xascendsessionsvrkey" type="text" id="xascendsessionsvrkey" value="{{ isset($radacct->xascendsessionsvrkey) ? $radacct->xascendsessionsvrkey : ''}}" >
    {!! $errors->first('xascendsessionsvrkey', '<p class="help-block">:message</p>') !!}
</div>


<div class="form-group">
    <input class="btn btn-primary" type="submit" value="{{ $formMode === 'edit' ? 'Update' : 'Create' }}">
</div>
