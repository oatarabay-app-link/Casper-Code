@extends('backend.layouts.app')

@section('content')
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Subscriptionprotocols</div>
                    <div class="card-body">
                        <a href="{{ url('/admin/subscription-protocols/create') }}" class="btn btn-success btn-sm" title="Add New SubscriptionProtocol">
                            <i class="fa fa-plus" aria-hidden="true"></i> Add New
                        </a>

                        <form method="GET" action="{{ url('/admin/subscription-protocols') }}" accept-charset="UTF-8" class="form-inline my-2 my-lg-0 float-right" role="search">
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
                                        <th>#</th><th>Subscription Uuid</th><th>Protocol Uuid</th><th>Protocol Id</th><th>Subscription Id</th><th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($subscriptionprotocols as $item)
                                    <tr>
                                        <td>{{ $item->id }}</td>
                                        <td>{{ $item->subscription_uuid }}</td><td>{{ $item->protocol_uuid }}</td><td>{{ $item->protocol_id }}</td><td>{{ $item->subscription_id }}</td>
                                        <td>
                                            <a href="{{ url('/admin/subscription-protocols/' . $item->id) }}" title="View SubscriptionProtocol"><button class="btn btn-info btn-sm"><i class="fa fa-eye" aria-hidden="true"></i> View</button></a>
                                            <a href="{{ url('/admin/subscription-protocols/' . $item->id . '/edit') }}" title="Edit SubscriptionProtocol"><button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button></a>

                                            <form method="POST" action="{{ url('/admin/subscription-protocols' . '/' . $item->id) }}" accept-charset="UTF-8" style="display:inline">
                                                {{ method_field('DELETE') }}
                                                {{ csrf_field() }}
                                                <button type="submit" class="btn btn-danger btn-sm" title="Delete SubscriptionProtocol" onclick="return confirm(&quot;Confirm delete?&quot;)"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <div class="pagination-wrapper"> {!! $subscriptionprotocols->appends(['search' => Request::get('search')])->render() !!} </div>
                        </div>

                    </div>
                </div>

@endsection
