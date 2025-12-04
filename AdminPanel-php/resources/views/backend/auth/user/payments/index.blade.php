@extends('backend.auth.user.show')

@section("inner_content")

                        <a href="{{ url('/admin/auth/user/'. $user->id .'/user-payments-logs/create') }}" class="btn btn-success btn-sm" title="Add New Payment">
                            <i class="fa fa-plus" aria-hidden="true"></i> Add New
                        </a>

                        <form method="GET" action="{{ url('/admin/auth/user/'. $user->id .'/user-payments-logs') }}" accept-charset="UTF-8" class="form-inline my-2 my-lg-0 float-right" role="search">
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
                                        <th>#</th>
                                       
                                        <th>Subscription #</th>
                                        <th>Date Created</th>
                                        <th>Months</th>
                                        <th>Payment ID</th>
                                        <th>Status</th>
                                        <th>Product</th>
                                        <th>Payment Sum</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($payments as $item)
                                    <tr>
                                        <td>{{ $item->id }}</td>
                                        <td>{{ $item->subscriptions->subscription_name }}</td>
                                        <td>{{ $item->create_date }}</td>
                                        <td>{{ $item->period_in_months }}</td>
                                        <td>{{ $item->payment_id }}</td>
                                        <td>{{ $item->status }}</td>
                                        <td>{{ $item->product_description}}</td>
                                        <td>{{ $item->payment_sum }}</td>

                                       
                                        <td>
                                            <a href="{{ url('/admin/auth/user/'. $user->id .'/user-payments-logs/' . $item->id) }}" title="View Payment"><button class="btn btn-info btn-sm"><i class="fa fa-eye" aria-hidden="true"></i> View</button></a>
                                            <a href="{{ url('/admin/auth/user/'. $user->id .'/user-payments-logs/' . $item->id . '/edit') }}" title="Edit Payment"><button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button></a>

                                            <form method="POST" action="{{ url('/admin/auth/user/'. $user->id .'/user-payments-logs' . '/' . $item->id) }}" accept-charset="UTF-8" style="display:inline">
                                                {{ method_field('DELETE') }}
                                                {{ csrf_field() }}
                                                <button type="submit" class="btn btn-danger btn-sm" title="Delete Payment" onclick="return confirm(&quot;Confirm delete?&quot;)"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <div class="pagination-wrapper"> {!! $payments->appends(['search' => Request::get('search')])->render() !!} </div>
                        </div>


@endsection
