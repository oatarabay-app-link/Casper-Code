@extends('backend.auth.user.show')

@section("inner_content")
                <div class="card">
                    <div class="card-header">User History {{ $userhistory->id }}</div>
                    <div class="card-body">

                        <a href="{{ url('/admin/auth/user/'. $user->id .'/user-history') }}" title="Back"><button class="btn btn-warning btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</button></a>
                        <a href="{{ url('/admin/auth/user/'. $user->id .'/user-history/' . $userhistory->id . '/edit') }}" title="Edit UserHistory"><button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button></a>

                        <form method="POST" action="{{ url('admin/auth/user/'. $user->id .'/userhistory' . '/' . $userhistory->id) }}" accept-charset="UTF-8" style="display:inline">
                            {{ method_field('DELETE') }}
                            {{ csrf_field() }}
                            <button type="submit" class="btn btn-danger btn-sm" title="Delete UserHistory" onclick="return confirm(&quot;Confirm delete?&quot;)"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</button>
                        </form>
                        <br/>
                        <br/>

                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <th>ID</th>
                                        <td>{{ $userhistory->id }}</td>
                                    </tr>
                                    <tr><th> User Id </th>
                                        <td> {{ $userhistory->user_id }} </td>
                                    </tr>
                                    <tr>
                                        <th> Event </th>
                                        <td> {{ $userhistory->event }} </td>
                                    </tr>
                                    <tr>
                                        <th> Operation </th>
                                        <td> {{ $userhistory->operation }} </td>
                                    </tr>
                                    <tr>
                                        <th> Result </th>
                                        <td> {{ $userhistory->result }} </td></tr><tr><th> Description </th><td> {{ $userhistory->description }} </td></tr>
                                </tbody>
                            </table>
                        </div>

        </div>
    </div>
@endsection
