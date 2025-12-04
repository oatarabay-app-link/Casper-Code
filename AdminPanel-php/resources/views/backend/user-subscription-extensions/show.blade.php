@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            @include('admin.sidebar')

            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">UserSubscriptionExtension {{ $usersubscriptionextension->id }}</div>
                    <div class="card-body">

                        <a href="{{ url('/admin/user-subscription-extensions') }}" title="Back"><button class="btn btn-warning btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</button></a>
                        <a href="{{ url('/admin/user-subscription-extensions/' . $usersubscriptionextension->id . '/edit') }}" title="Edit UserSubscriptionExtension"><button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button></a>

                        <form method="POST" action="{{ url('admin/usersubscriptionextensions' . '/' . $usersubscriptionextension->id) }}" accept-charset="UTF-8" style="display:inline">
                            {{ method_field('DELETE') }}
                            {{ csrf_field() }}
                            <button type="submit" class="btn btn-danger btn-sm" title="Delete UserSubscriptionExtension" onclick="return confirm(&quot;Confirm delete?&quot;)"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</button>
                        </form>
                        <br/>
                        <br/>

                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <th>ID</th><td>{{ $usersubscriptionextension->id }}</td>
                                    </tr>
                                    <tr><th> User Id </th><td> {{ $usersubscriptionextension->user_id }} </td></tr><tr><th> Subscription Id </th><td> {{ $usersubscriptionextension->subscription_id }} </td></tr><tr><th> Days </th><td> {{ $usersubscriptionextension->days }} </td></tr><tr><th> Exipry Date </th><td> {{ $usersubscriptionextension->exipry_date }} </td></tr><tr><th> Note </th><td> {{ $usersubscriptionextension->note }} </td></tr><tr><th> Added By </th><td> {{ $usersubscriptionextension->added_by }} </td></tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
