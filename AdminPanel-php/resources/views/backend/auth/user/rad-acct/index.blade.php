@extends('backend.auth.user.show')

@section("inner_content")

                        <form method="GET" action="{{ url('/admin/auth/user/'. $user->id .'/rad-acct') }}" accept-charset="UTF-8" class="form-inline my-2 my-lg-0 float-right" role="search">
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
                                        <th>ID</th><th>Server</th><th>Start Time</th><th>Stop Time</th><th>Session Time</th><th>Input Octet</th><th>Output Octet</th><th>User Ip</th><th>Terminate Cause</th><th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($radacct as $item)
                                    <tr>
                                        
                                        <td>{{ $item->radacctid }}</td><td>{{ $item->nasipaddress }}</td><td>{{ $item->acctstarttime }}</td><td>{{ $item->acctstoptime }}</td><td>{{ $item->acctsesslontime }}</td><td>{{ $item->acctinputoctet }}</td><td>{{ $item->acctoutputoctet }}</td><td>{{ $item->callingstationid }}</td><td>{{ $item->acctterminatecause }}</td>
                                        <td>
                                            <a href="{{ url('/admin/auth/user/'. $user->id .'/rad-acct/' . $item->id) }}" title="View RadAcct"><button class="btn btn-info btn-sm"><i class="fa fa-eye" aria-hidden="true"></i> View</button></a>
                                            <a href="{{ url('/admin/auth/user/'. $user->id .'/rad-acct/' . $item->id . '/edit') }}" title="Edit RadAcct"><button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button></a>

                                            <form method="POST" action="{{ url('/admin/auth/user/'. $user->id .'/rad-acct' . '/' . $item->id) }}" accept-charset="UTF-8" style="display:inline">
                                                {{ method_field('DELETE') }}
                                                {{ csrf_field() }}
                                                
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <div class="pagination-wrapper"> {!! $radacct->appends(['search' => Request::get('search')])->render() !!} </div>
                        </div>


@endsection
