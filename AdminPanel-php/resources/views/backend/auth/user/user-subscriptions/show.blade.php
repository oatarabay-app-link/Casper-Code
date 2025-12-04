@extends('backend.auth.user.show')

@section("inner_content")


                        <a href="{{ url('/admin/auth/user/'. $user->id .'/auth/user/'. $user->id .'/user-subscriptions') }}" title="Back"><button class="btn btn-warning btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</button></a>
                        <a href="{{ url('/admin/auth/user/'. $user->id .'/auth/user/'. $user->id .'/user-subscriptions/' . $usersubscription->id . '/edit') }}" title="Edit UserSubscription"><button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button></a>

                        <form method="POST" action="{{ url('/admin/auth/user/'. $user->id .'/auth/user/'. $user->id .'/usersubscriptions' . '/' . $usersubscription->id) }}" accept-charset="UTF-8" style="display:inline">
                            {{ method_field('DELETE') }}
                            {{ csrf_field() }}
                            <button type="submit" class="btn btn-danger btn-sm" title="Delete UserSubscription" onclick="return confirm(&quot;Confirm delete?&quot;)"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</button>
                        </form>
                        <br/>
                        <br/>

                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <th>ID</th><td>{{ $usersubscription->id }}</td>
                                    </tr>
                                    <tr><th> UUID </th>
                                        <td> {{ $usersubscription->uuid }} </td>
                                    </tr>
                                    <tr>
                                        <th> Subscription UUID </th>
                                        <td> {{ $usersubscription->subscription_uuid }} </td>
                                    </tr><tr><th>Start Date </th>
                                        <td> {{ $usersubscription->subscription_start_date }} </td>
                                    </tr><tr>
                                        <th> End Date </th>
                                        <td> {{ $usersubscription->subscription_end_date }} </td>
                                    </tr><tr>
                                        <th> VPN Pass </th>
                                        <td> {{ $usersubscription->vpn_pass }} </td>
                                    </tr><tr><th> Is Active </th>
                                        <td> {{ $usersubscription->is_active }} </td>
                                    </tr><tr>
                                        <th> Subscription </th>
                                        <td> {{ $usersubscription->subscriptions->subscription_name }} </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

@endsection