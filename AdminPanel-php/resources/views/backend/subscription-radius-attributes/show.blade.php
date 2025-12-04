@extends('backend.layouts.app')

@section('content')
    <div class="col-md-12">
        <div class="card">
                    <div class="card-header">SubscriptionRadiusAttribute {{ $subscriptionradiusattribute->id }}</div>
                    <div class="card-body">

                        <a href="{{ url('/admin/subscription-radius-attributes') }}" title="Back"><button class="btn btn-warning btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</button></a>
                        <a href="{{ url('/admin/subscription-radius-attributes/' . $subscriptionradiusattribute->id . '/edit') }}" title="Edit SubscriptionRadiusAttribute"><button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button></a>

                        <form method="POST" action="{{ url('admin/subscriptionradiusattributes' . '/' . $subscriptionradiusattribute->id) }}" accept-charset="UTF-8" style="display:inline">
                            {{ method_field('DELETE') }}
                            {{ csrf_field() }}
                            <button type="submit" class="btn btn-danger btn-sm" title="Delete SubscriptionRadiusAttribute" onclick="return confirm(&quot;Confirm delete?&quot;)"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</button>
                        </form>
                        <br/>
                        <br/>

                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <th>ID</th><td>{{ $subscriptionradiusattribute->id }}</td>
                                    </tr>
                                    <tr><th> Subscription Id </th><td> {{ $subscriptionradiusattribute->subscription_id }} </td></tr><tr><th> Attribute </th><td> {{ $subscriptionradiusattribute->attribute }} </td></tr><tr><th> Op </th><td> {{ $subscriptionradiusattribute->op }} </td></tr><tr><th> Value </th><td> {{ $subscriptionradiusattribute->value }} </td></tr><tr><th> Description </th><td> {{ $subscriptionradiusattribute->description }} </td></tr><tr><th> Status </th><td> {{ $subscriptionradiusattribute->status }} </td></tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
