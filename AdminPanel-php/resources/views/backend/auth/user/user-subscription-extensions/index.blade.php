@extends('backend.auth.user.show')

@section("inner_content")
                        <a href="{{ url('/admin/auth/user/'. $user->id .'/user-subscription-extensions/create') }}" class="btn btn-success btn-sm" title="Add New UserSubscriptionExtension">
                            <i class="fa fa-plus" aria-hidden="true"></i> Add New
                        </a>

                        <form method="GET" action="{{ url('/admin/auth/user/'. $user->id .'/user-subscription-extensions') }}" accept-charset="UTF-8" class="form-inline my-2 my-lg-0 float-right" role="search">
                            <div class="input-group">
                                <input type="text" class="form-control" name="search" placeholder="Search..." value="{{ request('search') }}">
                                <span class="input-group-append">
                                    <button class="btn btn-secondary" type="submit">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </span>
                            </div>
                        </form>

                        <br/>
                        <br/>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>#</th><th>Subscription #</th><th>Days</th><th>Expiry Date</th><th>Note</th><th>Added</th><th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($usersubscriptionextensions as $item)
                                    <tr>
                                        <td>{{ $item->id }}</td>
                                        <td>{{ $item->subscription_id }}</td><td>{{ $item->days }}</td><td>{{ $item->expiry_date }}</td><td>{{ $item->note }}</td><td>{{ $item->created_at}}</td>
                                        <td>
                                            <a href="{{ url('/admin/auth/user/'. $user->id .'/user-subscription-extensions/' . $item->id) }}" title="View UserSubscriptionExtension"><button class="btn btn-info btn-sm"><i class="fa fa-eye" aria-hidden="true"></i> View</button></a>
                                            {{--<a href="{{ url('/admin/auth/user/'. $user->id .'/user-subscription-extensions/' . $item->id . '/edit') }}" title="Edit UserSubscriptionExtension"><button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button></a>--}}
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <div class="pagination-wrapper"> {!! $usersubscriptionextensions->appends(['search' => Request::get('search')])->render() !!} </div>
                        </div>
@endsection
