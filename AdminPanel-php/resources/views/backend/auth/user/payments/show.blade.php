@extends('backend.auth.user.show')

@section("inner_content")
                <div class="card">
                    <div class="card-header">Payment {{ $payment->id }}</div>
                    <div class="card-body">

                        <a href="{{ url('/admin/auth/user/'. $user->id .'/user-payments-logs') }}" title="Back"><button class="btn btn-warning btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</button></a>
                        <a href="{{ url('/admin/auth/user/'. $user->id .'/user-payments-logs/' . $payment->id . '/edit') }}" title="Edit Payment"><button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button></a>

                        <form method="POST" action="{{ url('admin/auth/user/'. $user->id .'/user-payments-logs' . '/' . $payment->id) }}" accept-charset="UTF-8" style="display:inline">
                            {{ method_field('DELETE') }}
                            {{ csrf_field() }}
                            <button type="submit" class="btn btn-danger btn-sm" title="Delete Payment" onclick="return confirm(&quot;Confirm delete?&quot;)"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</button>
                        </form>
                        <br/>
                        <br/>

                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <th>ID</th><td>{{ $payment->id }}</td>
                                    </tr>
                                    <tr><th> Uuid </th><td> {{ $payment->uuid }} </td></tr><tr><th> Subscription Uuid </th><td> {{ $payment->subscription_uuid }} </td></tr><tr><th> Subscription Id </th><td> {{ $payment->subscription_id }} </td></tr><tr><th> User Id </th><td> {{ $payment->user_id }} </td></tr><tr><th> Period In Months </th><td> {{ $payment->period_in_months }} </td></tr><tr><th> Payment Id </th><td> {{ $payment->payment_id }} </td></tr><tr><th> Status </th><td> {{ $payment->status }} </td></tr><tr><th> Payment Sum </th><td> {{ $payment->payment_sum }} </td></tr><tr><th> Details </th><td> {{ $payment->details }} </td></tr><tr><th> Check Code </th><td> {{ $payment->check_code }} </td></tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>

@endsection
