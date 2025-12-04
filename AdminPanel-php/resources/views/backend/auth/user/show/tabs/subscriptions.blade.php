<div class="col">



    @foreach($user->subscriptions as $sub)



   <div class="table-responsive">
        <table class="table table-hover">
            <tr>
                <th>Subscription ID</th>
                <td>{{$sub->id}}</td>
            </tr>

            <tr>
                <th>Subscription UUID</th>
                <td>{{$sub->uuid}}</td>
            </tr>

            <tr>
                <th>Start Date</th>
                <td>{{$sub->subscription_start_date}}</td>
            </tr>

            <tr>
                <th>End Date</th>
                <td>{{$sub->subscription_end_date}}</td>
            </tr>

            <tr>
                <th>Status</th>
                <td>{{$sub->is_active}}</td>
            </tr>

            <tr>
                <th>Subscription Name</th>
                <td>{{$sub->subscriptions->subscription_name}}</td>
            </tr>

            <tr>
                <th>Days to Expiry</th>
                <td>TODO</td>
            </tr>

        </table>
    </div>

    @endforeach





            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <i class="fa fa-align-justify"></i> Payment Checks</div>
                        <div class="card-body">
                            <table class="table table-responsive-sm table-bordered table-striped table-sm">
                                <thead>
                                <tr>
                                    <th>UUID</th>
                                    <th>Date Created</th>
                                    <th>Status</th>
                                    <th>Subscription</th>
                                    <th>Token</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($user->payment_checks as $pcheck)
                                    <tr>
                                        <td>{{$pcheck->uuid}}</td>
                                        <td>{{$pcheck->create_date}}</td>
                                        <td>{{$pcheck->status}}</td>
                                        <td>{{$pcheck->subscriptions->subscription_name}}</td>
                                        <td>{{$pcheck->token}}</td>

                                    </tr>
                                @endforeach
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
                <!-- /.col-->
            </div>


        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> Payments</div>
                    <div class="card-body">
                        <table class="table table-responsive-sm table-bordered table-striped table-sm">
                            <thead>
                            <tr>
                                <th>UUID</th>
                                <th>Date Created</th>
                                <th>Status</th>
                                <th>Subscription</th>
                                <th>Months</th>
                                <th>Payment Sum</th>
                                <th>Check Code</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($user->payments as $pay)
                                <tr>
                                    <td>{{$pay->uuid}}</td>
                                    <td>{{$pay->create_date}}</td>
                                    <td>{{$pay->status}}</td>
                                    <td>{{$pay->subscriptions->subscription_name}}</td>
                                    <td>{{$pay->period_in_months}}</td>
                                    <td>{{$pay->payment_sum}}</td>
                                    <td>{{$pay->check_code}}</td>

                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
            <!-- /.col-->
        </div>


</div><!--table-responsive-->
