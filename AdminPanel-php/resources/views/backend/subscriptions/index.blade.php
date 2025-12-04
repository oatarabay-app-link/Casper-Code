@extends('backend.layouts.app')

@section('content')
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Subscriptions</div>
                    <div class="card-body">
                        <a href="{{ url('/admin/subscriptions/create') }}" class="btn btn-success btn-sm" title="Add New Subscription">
                            <i class="fa fa-plus" aria-hidden="true"></i> Add New
                        </a>

                        <form method="GET" action="{{ url('/admin/subscriptions') }}" accept-charset="UTF-8" class="form-inline my-2 my-lg-0 float-right" role="search">
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
                                        <th>Subscription Name</th>
                                        <th>Monthly Price</th>
                                        <th>Annual Price</th>
                                        <th>Currency Type</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($subscriptions as $item)
                                    <tr>
                                        <td>{{ $item->id }}</td>
                                        <td>{{ $item->subscription_name }}</td>
                                        <td>{{ $item->monthly_price }}</td>
                                        <td>{{ $item->period_price }}</td>
                                        <td>{{ $item->currency_type }}</td>
                                       
                                        <td>
                                            <a href="{{ url('/admin/subscriptions/' . $item->id) }}" title="View Subscription">
                                                <button class="btn btn-info btn-sm">
                                                    <i class="fa fa-eye" aria-hidden="true"></i> 
                                                View
                                            </button>
                                        </a>
                                            <a href="{{ url('/admin/subscriptions/' . $item->id . '/edit') }}" title="Edit Subscription">
                                                <button class="btn btn-primary btn-sm">
                                                    <i class="fa fa-pencil-square-o" aria-hidden="true">
                                                        
                                                    </i>
                                                     Edit
                                                 </button>
                                             </a>

                                            <form method="POST" action="{{ url('/admin/subscriptions' . '/' . $item->id) }}" accept-charset="UTF-8" style="display:inline">
                                                {{ method_field('DELETE') }}
                                                {{ csrf_field() }}
                                                <button type="submit" class="btn btn-danger btn-sm" title="Delete Subscription" onclick="return confirm(&quot;Confirm delete?&quot;)"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</button>
                                            </form>

                                            <a href="{{ url('/admin/subscription-radius-attributes')}}" title="Attributes">
                                                <button class="btn btn-warning btn-sm" style="color: white;">
                                                    <i class="fa fa-pencil-square-o" aria-hidden="true">
                                                        
                                                    </i>
                                                     Attributes
                                                 </button>
                                             </a>


                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <div class="pagination-wrapper"> {!! $subscriptions->appends(['search' => Request::get('search')])->render() !!} </div>
                        </div>

                    </div>
                </div>

@endsection
