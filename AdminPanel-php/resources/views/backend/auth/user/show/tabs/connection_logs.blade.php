<div class="col">
 
<div class="row">
From:
    <div class="col-sm-4">
       <input class="form-control" type="date" value="2018-08-19">
    </div>
 To:   
    <div class="col-sm-4">
       <input class="form-control" type="date" value="2018-09-19" >
    </div>

    <div class="col-sm-3">
       <form class="" action="" method="post"> 
         <button class="btn btn-warning" type="button">
         <i class="fa fa-search"></i> Search
         </button>

         <button class="btn btn-primary" type="button">
         Today
         </button>

         <button class="btn btn-danger" type="button">
         Last 10
         </button>
       </form>
    </div>


</div> <!-- row -->

</div>
   <!-- <div class="table-responsive">
        <table class="table table-hover">
            <tr>
                <th>@lang('labels.backend.access.users.tabs.content.overview.avatar')</th>
                <td><img src="{{ $user->picture }}" class="user-profile-image" /></td>
            </tr>

            <tr>
                <th>@lang('labels.backend.access.users.tabs.content.overview.name')</th>
                <td>{{ $user->name }} iiiii</td>
            </tr>

            <tr>
                <th>@lang('labels.backend.access.users.tabs.content.overview.email')</th>
                <td>{{ $user->email }}</td>
            </tr>

            <tr>
                <th>@lang('labels.backend.access.users.tabs.content.overview.status')</th>
                <td>{!! $user->status_label !!}</td>
            </tr>

            <tr>
                <th>@lang('labels.backend.access.users.tabs.content.overview.confirmed')</th>
                <td>{!! $user->confirmed_label !!}</td>
            </tr>

            <tr>
                <th>@lang('labels.backend.access.users.tabs.content.overview.timezone')</th>
                <td>{{ $user->timezone }}</td>
            </tr>

            <tr>
                <th>@lang('labels.backend.access.users.tabs.content.overview.last_login_at')</th>
                <td>
                    @if($user->last_login_at)
                        {{ timezone()->convertToLocal($user->last_login_at) }}
                    @else
                        N/A
                    @endif
                </td>
            </tr>

            <tr>
                <th>@lang('labels.backend.access.users.tabs.content.overview.last_login_ip')</th>
                <td>{{ $user->last_login_ip ?? 'N/A' }}</td>
            </tr>
        </table>
    </div> -->

