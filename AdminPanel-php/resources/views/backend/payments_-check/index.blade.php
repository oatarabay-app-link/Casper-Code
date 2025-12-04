@extends('backend.layouts.app')

@section('content')
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Payments_check</div>
                    <div class="card-body">
                        <a href="{{ url('/admin/payments_-check/create') }}" class="btn btn-success btn-sm" title="Add New Payments_Check">
                            <i class="fa fa-plus" aria-hidden="true"></i> Add New
                        </a>

                        <form method="GET" action="{{ url('/admin/payments_-check') }}" accept-charset="UTF-8" class="form-inline my-2 my-lg-0 float-right" role="search">
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
                                        <th>#</th><th>Uuid</th><th>Create Date</th><th>Subscription Uuid</th><th>User Uuid</th><th>User Id</th><th>User Email</th><th>Subscription Id</th><th>Token</th><th>Status</th><th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($payments_check as $item)
                                    <tr>
                                        <td>{{ $item->id }}</td>
                                        <td>{{ $item->uuid }}</td><td>{{ $item->create_date }}</td><td>{{ $item->subscription_uuid }}</td><td>{{ $item->user_uuid }}</td><td>{{ $item->user_id }}</td><td>{{ $item->user_email }}</td><td>{{ $item->subscription_id }}</td><td>{{ $item->token }}</td><td>{{ $item->status }}</td>
                                        <td>
                                            <a href="{{ url('/admin/payments_-check/' . $item->id) }}" title="View Payments_Check"><button class="btn btn-info btn-sm"><i class="fa fa-eye" aria-hidden="true"></i> View</button></a>
                                            <a href="{{ url('/admin/payments_-check/' . $item->id . '/edit') }}" title="Edit Payments_Check"><button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button></a>

                                            <form method="POST" action="{{ url('/admin/payments_-check' . '/' . $item->id) }}" accept-charset="UTF-8" style="display:inline">
                                                {{ method_field('DELETE') }}
                                                {{ csrf_field() }}
                                                <button type="submit" class="btn btn-danger btn-sm" title="Delete Payments_Check" onclick="return confirm(&quot;Confirm delete?&quot;)"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <div class="pagination-wrapper"> {!! $payments_check->appends(['search' => Request::get('search')])->render() !!} </div>
                        </div>

                    </div>
                </div>

@endsection
