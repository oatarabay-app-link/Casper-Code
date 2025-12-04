@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            @include('admin.sidebar')

            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">Radacct</div>
                    <div class="card-body">
                        <a href="{{ url('/admin/rad-acct/create') }}" class="btn btn-success btn-sm" title="Add New RadAcct">
                            <i class="fa fa-plus" aria-hidden="true"></i> Add New
                        </a>

                        <form method="GET" action="{{ url('/admin/rad-acct') }}" accept-charset="UTF-8" class="form-inline my-2 my-lg-0 float-right" role="search">
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
                                        <th>#</th><th>Raddacctid</th><th>Acctsessionid</th><th>Acctuniqueid</th><th>Username</th><th>Groupname</th><th>Realm</th><th>Nasipaddress</th><th>Nasidentifier</th><th>Nasportid</th><th>Nasporttype</th><th>Acctstarttime</th><th>Acctstoptime</th><th>Acctsesslontime</th><th>Acctauthentic</th><th>Connectinfo Start</th><th>Connectinfo Stop</th><th>Acctinputoctest</th><th>Acctoutputoctest</th><th>Calledstationid</th><th>Callingstationid</th><th>Acctterminatecause</th><th>Servicetype</th><th>Framedprotocol</th><th>Framedipaddress</th><th>Acctstartdelay</th><th>Acctstopdelay</th><th>Xascendsessionsvrkey</th><th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($radacct as $item)
                                    <tr>
                                        <td>{{ $item->id }}</td>
                                        <td>{{ $item->raddacctid }}</td><td>{{ $item->acctsessionid }}</td><td>{{ $item->acctuniqueid }}</td><td>{{ $item->username }}</td><td>{{ $item->groupname }}</td><td>{{ $item->realm }}</td><td>{{ $item->nasipaddress }}</td><td>{{ $item->nasidentifier }}</td><td>{{ $item->nasportid }}</td><td>{{ $item->nasporttype }}</td><td>{{ $item->acctstarttime }}</td><td>{{ $item->acctstoptime }}</td><td>{{ $item->acctsesslontime }}</td><td>{{ $item->acctauthentic }}</td><td>{{ $item->connectinfo_start }}</td><td>{{ $item->connectinfo_stop }}</td><td>{{ $item->acctinputoctest }}</td><td>{{ $item->acctoutputoctest }}</td><td>{{ $item->calledstationid }}</td><td>{{ $item->callingstationid }}</td><td>{{ $item->acctterminatecause }}</td><td>{{ $item->servicetype }}</td><td>{{ $item->framedprotocol }}</td><td>{{ $item->framedipaddress }}</td><td>{{ $item->acctstartdelay }}</td><td>{{ $item->acctstopdelay }}</td><td>{{ $item->xascendsessionsvrkey }}</td>
                                        <td>
                                            <a href="{{ url('/admin/rad-acct/' . $item->id) }}" title="View RadAcct"><button class="btn btn-info btn-sm"><i class="fa fa-eye" aria-hidden="true"></i> View</button></a>
                                            <a href="{{ url('/admin/rad-acct/' . $item->id . '/edit') }}" title="Edit RadAcct"><button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button></a>

                                            <form method="POST" action="{{ url('/admin/rad-acct' . '/' . $item->id) }}" accept-charset="UTF-8" style="display:inline">
                                                {{ method_field('DELETE') }}
                                                {{ csrf_field() }}
                                                <button type="submit" class="btn btn-danger btn-sm" title="Delete RadAcct" onclick="return confirm(&quot;Confirm delete?&quot;)"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <div class="pagination-wrapper"> {!! $radacct->appends(['search' => Request::get('search')])->render() !!} </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
