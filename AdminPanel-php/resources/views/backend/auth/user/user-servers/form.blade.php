<div class="form-group {{ $errors->has('user_id') ? 'has-error' : ''}}">
    {{--<label for="user_id" class="control-label">{{ 'User Id' }}</label>--}}
    <input class="form-control" name="user_id" type="hidden" id="user_id"
           value="{{ isset($userserver->user_id) ? $userserver->user_id : $user->id}}">
    {!! $errors->first('user_id', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('vpnserver_id') ? 'has-error' : ''}}">
    <label for="vpnserver_id" class="control-label">{{ 'VPN server' }}</label>

    <?php
    $items = $servers;
    $selected_id = isset($userserver->vpnserver_id) ? $userserver->vpnserver_id : '';
    ?>

    <select class="form-control" name="vpnserver_id" type="number" id="vpnserver_id">

        @foreach($items as $item)
            <option value="{{ $item->id }}"
                    @if( $selected_id==$item->id ) selected="selected" @endif> {{ $item->nas_fqdn}} - {{ $item->ip}} </option>
        @endforeach
    </select>

    {{--<input class="form-control" name="vpnserver_id" type="text" id="vpnserver_id" value="{{ isset($userserver->vpnserver_id) ? $userserver->vpnserver_id : ''}}" >--}}
    {!! $errors->first('vpnserver_id', '<p class="help-block">:message</p>') !!}
</div>


<div class="form-group">
    <input class="btn btn-primary" type="submit" value="{{ $formMode === 'edit' ? 'Update' : 'Create' }}">
</div>


