@extends('backend.layouts.app')

@section('content')
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Service {{ $service->id }}</div>
                    <div class="card-body">

                        <a href="{{ url('/admin/services') }}" title="Back"><button class="btn btn-warning btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</button></a>
                        <a href="{{ url('/admin/services/' . $service->id . '/edit') }}" title="Edit Service"><button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button></a>

                        <form method="POST" action="{{ url('admin/services' . '/' . $service->id) }}" accept-charset="UTF-8" style="display:inline">
                            {{ method_field('DELETE') }}
                            {{ csrf_field() }}
                            <button type="submit" class="btn btn-danger btn-sm" title="Delete Service" onclick="return confirm(&quot;Confirm delete?&quot;)"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</button>
                        </form>
                        <br/>
                        <br/>

                        <div class="row">
                            <div class="col-sm-6">

                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <th>ID</th><td>{{ $service->id }}</td>
                                    </tr>
                                    <tr><th> Name </th><td> {{ $service->name }} </td></tr><tr><th> Type </th><td> {{ $service->type }} </td></tr><tr><th> Amount </th><td> {{ $service->amount }} </td></tr><tr><th> Is Recurring </th><td> {{ $service->is_recurring }} </td></tr><tr><th> Is Autobill </th><td> {{ $service->is_autobill }} </td></tr><tr><th> Setup Fee </th><td> {{ $service->setup_fee }} </td></tr>
                                </tbody>
                            </table>
                        </div>

                    </div>

                    <div class="col-sm-6">
                       
                         <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                    <tr><th> Purchase Date </th><td> {{ $service->purchase_date }} </td></tr><tr><th> Renewal Date </th><td> {{ $service->renewal_date }} </td></tr><tr><th> Notify </th><td> {{ $service->notify }} </td></tr><tr><th> Notify Days </th><td> {{ $service->notify_days }} </td></tr><tr><th> Service Provider Id </th><td> {{ $service->service_provider_id }} </td></tr><tr><th> Notes </th><td> {{ $service->notes }} </td></tr>
                                </tbody>
                            </table>
                        </div> 

                    </div>


                </div>




                    </div>
                </div>

@endsection
