@extends('backend.layouts.app')

@section('content')
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Subscription #{{ $subscription->id }} Name: {{ $subscription->subscription_name }}</div>
                    <div class="card-body">

                        <a href="{{ url('/admin/subscriptions') }}" title="Back"><button class="btn btn-warning btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</button></a>
                        <a href="{{ url('/admin/subscriptions/' . $subscription->id . '/edit') }}" title="Edit Subscription"><button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button></a>

                        <form method="POST" action="{{ url('admin/subscriptions' . '/' . $subscription->id) }}" accept-charset="UTF-8" style="display:inline">
                            {{ method_field('DELETE') }}
                            {{ csrf_field() }}
                            <button type="submit" class="btn btn-danger btn-sm" title="Delete Subscription" onclick="return confirm(&quot;Confirm delete?&quot;)"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</button>
                        </form>
                        <br/>
                        <br/>

                        
                        <div class="row">

                            <div class="col-sm-6">

                        <div class="table-responsive">

    
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <th>ID</th>
                                        <td>{{ $subscription->id }}</td>
                                    </tr>

                                    <tr>
                                        <th> UUID </th>
                                        <td> {{ $subscription->uuid }} </td>
                                    </tr>
                                    <tr>
                                        <th> Subscription Name </th>
                                        <td> {{ $subscription->subscription_name }} </td>
                                    </tr>
                                    <tr>
                                        <th> Monthly Price </th>
                                        <td> {{ $subscription->monthly_price }} </td>
                                    </tr>
                                    <tr>
                                        <th> Period Price </th>
                                        <td> {{ $subscription->period_price }} </td>
                                    </tr>
                                    <tr>
                                        <th> Currency Type </th>
                                        <td> {{ $subscription->currency_type }} </td>
                                    </tr>
                                    <tr>
                                        <th> Traffic Size </th>
                                        <td> {{ $subscription->traffic_size }} </td>
                                    </tr>
                                    <tr>
                                        <th> Rate Limit </th>
                                        <td> {{ $subscription->rate_limit }} </td>
                                    </tr>
                                    
                                </tbody>
                            </table>
                        </div>

                    </div>

                    <div class="col-sm-6">
                        
                         <div class="table-responsive">

    
                            <table class="table">
                                <tbody>
                                      <tr>
                                        <th> Max Connections </th>
                                        <td> {{ $subscription->max_connections }} </td>
                                    </tr>
                                    <tr>
                                        <th> Available For Android </th>
                                        <td> {{ $subscription->available_for_android }} </td>
                                    </tr>
                                    <tr>
                                        <th> Available For Ios </th>
                                        <td> {{ $subscription->available_for_ios }} </td>
                                    </tr>
                                    <tr>
                                        <th> Create Time </th>
                                        <td> {{ $subscription->create_time }} </td>
                                    </tr>
                                    <tr>
                                        <th> Is Default </th>
                                        <td> {{ $subscription->is_default }} </td>
                                    </tr>
                                    <tr>
                                        <th> Period Length </th>
                                        <td> {{ $subscription->period_length }} </td>
                                    </tr>
                                    <tr>
                                        <th> Order Num </th>
                                        <td> {{ $subscription->order_num }} </td>
                                    </tr>
                                    <tr>
                                        <th> Product Id </th>
                                        <td> {{ $subscription->product_id }} </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>

                    </div>
                </div>

@endsection
