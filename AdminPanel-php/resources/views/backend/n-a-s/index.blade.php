@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            @include('admin.sidebar')

            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">Nas</div>
                    <div class="card-body">
                        <a href="{{ url('/admin/n-a-s/create') }}" class="btn btn-success btn-sm" title="Add New NA">
                            <i class="fa fa-plus" aria-hidden="true"></i> Add New
                        </a>

                        <form method="GET" action="{{ url('/admin/n-a-s') }}" accept-charset="UTF-8" class="form-inline my-2 my-lg-0 float-right" role="search">
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
                                        <th>#</th><th>Nasname</th><th>Shortname</th><th>Type</th><th>Ports</th><th>Secret</th><th>Server</th><th>Community</th><th>Description</th><th>Details</th><th>Check Code</th><th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($nas as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->nasname }}</td><td>{{ $item->shortname }}</td><td>{{ $item->type }}</td><td>{{ $item->ports }}</td><td>{{ $item->secret }}</td><td>{{ $item->server }}</td><td>{{ $item->community }}</td><td>{{ $item->description }}</td><td>{{ $item->details }}</td><td>{{ $item->check_code }}</td>
                                        <td>
                                            <a href="{{ url('/admin/n-a-s/' . $item->id) }}" title="View NA"><button class="btn btn-info btn-sm"><i class="fa fa-eye" aria-hidden="true"></i> View</button></a>
                                            <a href="{{ url('/admin/n-a-s/' . $item->id . '/edit') }}" title="Edit NA"><button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button></a>

                                            <form method="POST" action="{{ url('/admin/n-a-s' . '/' . $item->id) }}" accept-charset="UTF-8" style="display:inline">
                                                {{ method_field('DELETE') }}
                                                {{ csrf_field() }}
                                                <button type="submit" class="btn btn-danger btn-sm" title="Delete NA" onclick="return confirm(&quot;Confirm delete?&quot;)"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <div class="pagination-wrapper"> {!! $nas->appends(['search' => Request::get('search')])->render() !!} </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
