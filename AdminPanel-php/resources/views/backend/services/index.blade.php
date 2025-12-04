@extends('backend.layouts.app')

@section('content')
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Services</div>
                    <div class="card-body">
                        <a href="{{ url('/admin/services/create') }}" class="btn btn-success btn-sm" title="Add New Service">
                            <i class="fa fa-plus" aria-hidden="true"></i> Add New
                        </a>

                        <form method="GET" action="{{ url('/admin/services') }}" accept-charset="UTF-8" class="form-inline my-2 my-lg-0 float-right" role="search">
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
                                        <th>Name</th>
                                        <th>Type</th>
                                        <th>Amount</th>
                                        <th>Is Recurring</th>
                                        <th>Is Autobill</th><th>Setup Fee</th><th>Purchase Date</th><th>Renewal Date</th>
                                        <th>Service Provider Id</th><th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($services as $item)
                                    <tr>
                                        <td>{{ $item->id }}</td>
                                        <td>{{ $item->name }}</td><td>{{ $item->type }}</td><td>{{ $item->amount }}</td><td>{{ $item->is_recurring }}</td><td>{{ $item->is_autobill }}</td><td>{{ $item->setup_fee }}</td><td>{{ $item->purchase_date }}</td><td>{{ $item->renewal_date }}</td><td>{{ $item->service_provider_id }}</td>
                                        <td>
                                            <a href="{{ url('/admin/services/' . $item->id) }}" title="View Service"><button class="btn btn-info btn-sm"><i class="fa fa-eye" aria-hidden="true"></i> View</button></a>
                                            <a href="{{ url('/admin/services/' . $item->id . '/edit') }}" title="Edit Service"><button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button></a>

                                            <form method="POST" action="{{ url('/admin/services' . '/' . $item->id) }}" accept-charset="UTF-8" style="display:inline">
                                                {{ method_field('DELETE') }}
                                                {{ csrf_field() }}
                                                <button type="submit" class="btn btn-danger btn-sm" title="Delete Service" onclick="return confirm(&quot;Confirm delete?&quot;)"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <div class="pagination-wrapper"> {!! $services->appends(['search' => Request::get('search')])->render() !!} </div>
                        </div>

                    </div>
                </div>

@endsection
