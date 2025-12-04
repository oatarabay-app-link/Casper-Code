<div class="col">
    <div class="table-responsive">
        <table class="table table-hover">
            <tr>
                <th>@lang('labels.backend.access.users.tabs.content.overview.avatar')</th>
                <td><img src="{{ $user->picture }}" class="user-profile-image" /></td>
            </tr>

            <tr>
                <th>UUID</th>
                <td>{{ $user->uuid }}</td>
            </tr>

            <tr>
                <th>@lang('labels.backend.access.users.tabs.content.overview.name')</th>
                <td>{{ $user->name }}</td>
            </tr>


            <tr>
                <th>@lang('labels.backend.access.users.tabs.content.overview.email')</th>
                <td>{{ $user->email }}</td>
            </tr>

            <tr>
                <th>@lang('labels.backend.access.users.tabs.content.overview.status')</th>
                <td>@include('backend.auth.user.includes.status', ['user' => $user])</td>
            </tr>

            <tr>
                <th>User Status</th>
                <td>Todo</td>
            </tr>

            <tr>
                <th>Subscription Status</th>
                <td>TODO</td>
            </tr>

            <tr>
                <th>@lang('labels.backend.access.users.tabs.content.overview.confirmed')</th>
                <td>@include('backend.auth.user.includes.confirm', ['user' => $user])</td>
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



            <tr>
                <th>Affiliate</th>
                <td></td>
            </tr>

            <tr>
                <th>Last Connected</th>
                <td><a href="">server</a></td>
            </tr>

            <tr>
                <th>Issues</th>
                <td><a href="">0</a></td>
            </tr>

            <tr>
                <th>Location</th>
                <td></td>
            </tr>

            <tr>
                <th>Billing Cycle</th>
                <td></td>
            </tr>

            <tr>
                <th>Next Billing Date</th>
                <td></td>
            </tr>
        </table>



        <div class="card">
<div class="card-body">
<div class="row">
<div class="col-sm-5">
<h4 class="card-title mb-0">Traffic</h4>
<div class="small text-muted">November 2017</div>
</div>

<div class="col-sm-7 d-none d-md-block">
<button class="btn btn-primary float-right" type="button">
<i class="icon-cloud-download"></i>
</button>
<div class="btn-group btn-group-toggle float-right mr-3" data-toggle="buttons">
<label class="btn btn-outline-secondary">
<input id="option1" type="radio" name="options" autocomplete="off"> Day
</label>
<label class="btn btn-outline-secondary active">
<input id="option2" type="radio" name="options" autocomplete="off" checked=""> Month
</label>
<label class="btn btn-outline-secondary">
<input id="option3" type="radio" name="options" autocomplete="off"> Year
</label>
</div>
</div>

</div>

<div class="chart-wrapper" style="height:300px;margin-top:40px;"><div class="chartjs-size-monitor" style="position: absolute; left: 0px; top: 0px; right: 0px; bottom: 0px; overflow: hidden; pointer-events: none; visibility: hidden; z-index: -1;"><div class="chartjs-size-monitor-expand" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;"><div style="position:absolute;width:1000000px;height:1000000px;left:0;top:0"></div></div><div class="chartjs-size-monitor-shrink" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;"><div style="position:absolute;width:200%;height:200%;left:0; top:0"></div></div></div>
<canvas class="chart chartjs-render-monitor" id="main-chart" height="300" width="1047" style="display: block;"></canvas>
</div>
</div>
<div class="card-footer">
<div class="row text-center">
<div class="col-sm-12 col-md mb-sm-2 mb-0">
<div class="text-muted">Visits</div>
<strong>29.703 Users (40%)</strong>
<div class="progress progress-xs mt-2">
<div class="progress-bar bg-success" role="progressbar" style="width: 40%" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
</div>
</div>
<div class="col-sm-12 col-md mb-sm-2 mb-0">
<div class="text-muted">Unique</div>
<strong>24.093 Users (20%)</strong>
<div class="progress progress-xs mt-2">
<div class="progress-bar bg-info" role="progressbar" style="width: 20%" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
</div>
</div>
<div class="col-sm-12 col-md mb-sm-2 mb-0">
<div class="text-muted">Pageviews</div>
<strong>78.706 Views (60%)</strong>
<div class="progress progress-xs mt-2">
<div class="progress-bar bg-warning" role="progressbar" style="width: 60%" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
</div>
</div>
<div class="col-sm-12 col-md mb-sm-2 mb-0">
<div class="text-muted">New Users</div>
<strong>22.123 Users (80%)</strong>
<div class="progress progress-xs mt-2">
<div class="progress-bar bg-danger" role="progressbar" style="width: 80%" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
</div>
</div>
<div class="col-sm-12 col-md mb-sm-2 mb-0">
<div class="text-muted">Bounce Rate</div>
<strong>40.15%</strong>
<div class="progress progress-xs mt-2">
<div class="progress-bar" role="progressbar" style="width: 40%" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
</div>
</div>
</div>
</div>
</div>





    </div>
</div><!--table-responsive-->
