@extends('backend.layouts.app')

@section('content')
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Vpnserverprotocols</div>
                    <div class="card-body">
                        <a href="{{ url('/admin/v-p-n-server-protocols/create') }}" class="btn btn-success btn-sm" title="Add New VPNServerProtocol">
                            <i class="fa fa-plus" aria-hidden="true"></i> Add New
                        </a>

                        <form method="GET" action="{{ url('/admin/v-p-n-server-protocols') }}" accept-charset="UTF-8" class="form-inline my-2 my-lg-0 float-right" role="search">
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
                                        <th>#</th><th>Vpnserver Uuid</th><th>Protocol Uuid</th><th>Vpnserver Id</th><th>Protocol Id</th><th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($vpnserverprotocols as $item)
                                    <tr>
                                        <td>{{ $item->id }}</td>
                                        <td>{{ $item->vpnserver_uuid }}</td><td>{{ $item->protocol_uuid }}</td><td>{{ $item->vpnserver_id }}</td><td>{{ $item->protocol_id }}</td>
                                        <td>
                                            <a href="{{ url('/admin/v-p-n-server-protocols/' . $item->id) }}" title="View VPNServerProtocol"><button class="btn btn-info btn-sm"><i class="fa fa-eye" aria-hidden="true"></i> View</button></a>
                                            <a href="{{ url('/admin/v-p-n-server-protocols/' . $item->id . '/edit') }}" title="Edit VPNServerProtocol"><button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button></a>

                                            <form method="POST" action="{{ url('/admin/v-p-n-server-protocols' . '/' . $item->id) }}" accept-charset="UTF-8" style="display:inline">
                                                {{ method_field('DELETE') }}
                                                {{ csrf_field() }}
                                                <button type="submit" class="btn btn-danger btn-sm" title="Delete VPNServerProtocol" onclick="return confirm(&quot;Confirm delete?&quot;)"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <div class="pagination-wrapper"> {!! $vpnserverprotocols->appends(['search' => Request::get('search')])->render() !!} </div>
                        </div>

                    </div>
                </div>

@endsection
