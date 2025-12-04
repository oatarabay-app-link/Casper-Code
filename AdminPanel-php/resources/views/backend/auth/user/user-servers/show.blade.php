@extends('backend.auth.user.show')

@section("inner_content")
                        <a href="{{ url('/admin/auth/user/'. $user->id .'/user-servers') }}" title="Back"><button class="btn btn-warning btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</button></a>
                        <a href="{{ url('/admin/auth/user/'. $user->id .'/user-servers/' . $userserver->id . '/edit') }}" title="Edit UserServer"><button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button></a>

                        <form method="POST" action="{{ url('admin/auth/user/'. $user->id .'/userservers' . '/' . $userserver->id) }}" accept-charset="UTF-8" style="display:inline">
                            {{ method_field('DELETE') }}
                            {{ csrf_field() }}
                            <button type="submit" class="btn btn-danger btn-sm" title="Delete UserServer" onclick="return confirm(&quot;Confirm delete?&quot;)"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</button>
                        </form>
                        <br/>
                        <br/>

                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <th>ID</th><td>{{ $userserver->id }}</td>
                                    </tr>
                                    <tr><th> User Id </th><td> {{ $userserver->user_id }} </td></tr><tr><th> Vpnserver Id </th><td> {{ $userserver->vpnserver_id }} </td></tr>
                                </tbody>
                            </table>
                        </div>
@endsection
