@extends('backend.auth.user.show')

@section("inner_content")

                        <a href="{{ url('/admin/auth/user/'. $user->id .'/user-radius-attributes') }}" title="Back"><button class="btn btn-warning btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</button></a>
                        <a href="{{ url('/admin/auth/user/'. $user->id .'/user-radius-attributes/' . $userradiusattribute->id . '/edit') }}" title="Edit UserRadiusAttribute"><button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button></a>

                        <form method="POST" action="{{ url('admin/auth/user/'. $user->id .'/userradiusattributes' . '/' . $userradiusattribute->id) }}" accept-charset="UTF-8" style="display:inline">
                            {{ method_field('DELETE') }}
                            {{ csrf_field() }}
                            <button type="submit" class="btn btn-danger btn-sm" title="Delete UserRadiusAttribute" onclick="return confirm(&quot;Confirm delete?&quot;)"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</button>
                        </form>
                        <br/>
                        <br/>

                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <th>ID</th><td>{{ $userradiusattribute->id }}</td>
                                    </tr>
                                    <tr><th> User Id </th><td> {{ $userradiusattribute->user_id }} </td></tr><tr><th> Attribute </th><td> {{ $userradiusattribute->attribute }} </td></tr><tr><th> Op </th><td> {{ $userradiusattribute->op }} </td></tr><tr><th> Value </th><td> {{ $userradiusattribute->value }} </td></tr><tr><th> Description </th><td> {{ $userradiusattribute->description }} </td></tr><tr><th> Status </th><td> {{ $userradiusattribute->status }} </td></tr>
                                </tbody>
                            </table>
                        </div>
@endsection
