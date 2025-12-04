@extends('backend.layouts.app')

@section('content')
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">VPN servers</div>
                    <div class="card-body">
                        <a href="{{ url('/admin/v-p-n-servers/create') }}" class="btn btn-success btn-sm" title="Add New VPNServer">
                            <i class="fa fa-plus" aria-hidden="true"></i> Add New
                        </a>

                        <form method="GET" action="{{ url('/admin/v-p-n-servers') }}" accept-charset="UTF-8" class="form-inline my-2 my-lg-0 float-right" role="search">
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
                                        <th>#</th><th>Name</th><th>Ip</th><th>Port</th><th>Country</th><th>Server Provider</th><th>Latitude</th><th>Longitude</th><th>Create Date</th><th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($vpnservers as $item)
                                    <tr>
                                        <td>{{ $item->id }}</td><td>{{ $item->name }}</td>
                                        <td>{{ $item->ip }}</td>
                                        <td>{{ $item->port }}</td><td>{{ $item->country }}</td><td>{{ $item->server_provider }}</td><td>{{ $item->latitude }}</td><td>{{ $item->longitude }}</td><td>{{ $item->create_date }}</td>
                                        <td>
                                            <a href="{{ url('/admin/v-p-n-servers/' . $item->id) }}" title="View VPNServer"><button class="btn btn-info btn-sm"><i class="fa fa-eye" aria-hidden="true"></i> View</button></a>
                                            <a href="{{ url('/admin/v-p-n-servers/' . $item->id . '/edit') }}" title="Edit VPNServer"><button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button></a>

                                            <form method="POST" action="{{ url('/admin/v-p-n-servers' . '/' . $item->id) }}" accept-charset="UTF-8" style="display:inline">
                                                {{ method_field('DELETE') }}
                                                {{ csrf_field() }}
                                                <button type="submit" class="btn btn-danger btn-sm" title="Delete VPNServer" onclick="return confirm(&quot;Confirm delete?&quot;)"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <div class="pagination-wrapper"> {!! $vpnservers->appends(['search' => Request::get('search')])->render() !!} </div>
                        </div>

                    </div>
                </div>

@endsection
