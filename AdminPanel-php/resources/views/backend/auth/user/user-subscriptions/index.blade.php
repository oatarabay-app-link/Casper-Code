@extends('backend.auth.user.show')

@section("inner_content")
    {{--<a href="{{ url('/admin/auth/user/'. $user->id .'/auth/user/'. $user->id .'/user-subscriptions/create') }}" class="btn btn-success btn-sm"--}}
       {{--title="Add New UserSubscription">--}}
        {{--<i class="fa fa-plus" aria-hidden="true"></i> Add New--}}
    {{--</a>--}}
    {{--<form method="GET" action="{{ url('/admin/auth/user/'. $user->id .'/auth/user/'. $user->id .'/user-subscriptions') }}" accept-charset="UTF-8"--}}
          {{--class="form-inline my-2 my-lg-0 float-right" role="search">--}}
        {{--<div class="input-group">--}}
            {{--<input type="text" class="form-control" name="search" placeholder="Search..."--}}
                   {{--value="{{ request('search') }}">--}}
            {{--<span class="input-group-append">--}}
                                    {{--<button class="btn btn-secondary" type="submit">--}}
                                        {{--<i class="fa fa-search"></i>--}}
                                    {{--</button>--}}
                                {{--</span>--}}
        {{--</div>--}}
    {{--</form>--}}

    {{--<br/>--}}
    {{--<br/>--}}
    <div class="table-responsive">
        <table class="table">
            <thead>
            <tr>
                <th>#</th>

                <th>Start Date</th>
                <th>End Date</th>
                <th>VPN Pass</th>
                <th>Is Active</th>
                <th>Subscription</th>

                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach($usersubscriptions as $item)
                <tr>
                    <td>{{ $item->id }}</td>
                    <td>{{ $item->subscription_start_date }}</td>
                    <td>{{ $item->subscription_end_date }}</td>
                    <td>{{ $item->vpn_pass }}</td>
                    <td>{{ $item->is_active }}</td>
                    <td>{{ $item->subscriptions->subscription_name }}</td>
                    <td>
                        <a href="{{ url('/admin/auth/user/'. $user->id .'/user-subscriptions/' . $item->id) }}" title="View UserSubscription">
                            <button class="btn btn-info btn-sm"><i class="fa fa-eye" aria-hidden="true"></i> View
                            </button>
                        </a>
                        <a href="{{ url('/admin/auth/user/'. $user->id .'/auth/user/'. $user->id .'/user-subscriptions/' . $item->id . '/edit') }}"
                           title="Edit UserSubscription">
                            <button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o"
                                                                      aria-hidden="true"></i> Edit
                            </button>
                        </a>

                        <form method="POST" action="{{ url('/admin/auth/user/'. $user->id .'/auth/user/'. $user->id .'/user-subscriptions' . '/' . $item->id) }}"
                              accept-charset="UTF-8" style="display:inline">
                            {{ method_field('DELETE') }}
                            {{ csrf_field() }}
                            <button type="submit" class="btn btn-danger btn-sm" title="Delete UserSubscription"
                                    onclick="return confirm(&quot;Confirm delete?&quot;)"><i class="fa fa-trash-o"
                                                                                             aria-hidden="true"></i>
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="pagination-wrapper"> {!! $usersubscriptions->appends(['search' => Request::get('search')])->render() !!} </div>
    </div>


@endsection


