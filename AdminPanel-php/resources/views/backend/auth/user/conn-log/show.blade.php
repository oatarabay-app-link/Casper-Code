@extends('backend.auth.user.show')

@section("inner_content")

{{--                        <a href="{{ url('/admin/auth/user/'. $user->id .'/rad-acct') }}" title="Back"><button class="btn btn-warning btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</button></a>--}}
{{--                        <a href="{{ url('/admin/auth/user/'. $user->id .'/rad-acct/' . $radacct->id . '/edit') }}" title="Edit RadAcct"><button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button></a>--}}

{{--                        <form method="POST" action="{{ url('admin/auth/user/'. $user->id .'/radacct' . '/' . $radacct->id) }}" accept-charset="UTF-8" style="display:inline">--}}
{{--                            {{ method_field('DELETE') }}--}}
{{--                            {{ csrf_field() }}--}}
{{--                            <button type="submit" class="btn btn-danger btn-sm" title="Delete RadAcct" onclick="return confirm(&quot;Confirm delete?&quot;)"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</button>--}}
{{--                        </form>--}}
{{--                        <br/>--}}
{{--                        <br/>--}}

{{--                        <div class="table-responsive">--}}
{{--                            <table class="table">--}}
{{--                                <tbody>--}}
{{--                                    <tr>--}}
{{--                                        <th>ID</th><td>{{ $radacct->id }}</td>--}}
{{--                                    </tr>--}}
{{--                                    <tr><th> Groupname </th><td> {{ $radacct->groupname }} </td></tr><tr><th> Realm </th><td> {{ $radacct->realm }} </td></tr><tr><th> Nasipaddress </th><td> {{ $radacct->nasipaddress }} </td></tr><tr><th> Nasidentifier </th><td> {{ $radacct->nasidentifier }} </td></tr><tr><th> Nasportid </th><td> {{ $radacct->nasportid }} </td></tr><tr><th> Nasporttype </th><td> {{ $radacct->nasporttype }} </td></tr><tr><th> Acctstarttime </th><td> {{ $radacct->acctstarttime }} </td></tr><tr><th> Acctstoptime </th><td> {{ $radacct->acctstoptime }} </td></tr><tr><th> Acctsesslontime </th><td> {{ $radacct->acctsesslontime }} </td></tr><tr><th> Acctauthentic </th><td> {{ $radacct->acctauthentic }} </td></tr><tr><th> Connectinfo Start </th><td> {{ $radacct->connectinfo_start }} </td></tr><tr><th> Connectinfo Stop </th><td> {{ $radacct->connectinfo_stop }} </td></tr><tr><th> Acctinputoctest </th><td> {{ $radacct->acctinputoctest }} </td></tr><tr><th> Acctoutputoctest </th><td> {{ $radacct->acctoutputoctest }} </td></tr><tr><th> Calledstationid </th><td> {{ $radacct->calledstationid }} </td></tr><tr><th> Callingstationid </th><td> {{ $radacct->callingstationid }} </td></tr><tr><th> Acctterminatecause </th><td> {{ $radacct->acctterminatecause }} </td></tr><tr><th> Servicetype </th><td> {{ $radacct->servicetype }} </td></tr><tr><th> Framedprotocol </th><td> {{ $radacct->framedprotocol }} </td></tr><tr><th> Framedipaddress </th><td> {{ $radacct->framedipaddress }} </td></tr><tr><th> Acctstartdelay </th><td> {{ $radacct->acctstartdelay }} </td></tr><tr><th> Acctstopdelay </th><td> {{ $radacct->acctstopdelay }} </td></tr><tr><th> Xascendsessionsvrkey </th><td> {{ $radacct->xascendsessionsvrkey }} </td></tr>--}}
{{--                                </tbody>--}}
{{--                            </table>--}}
{{--                        </div>--}}
@endsection
