@extends('backend.layouts.app')

@section('content')
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Payments_Check {{ $payments_check->id }}</div>
                    <div class="card-body">

                        <a href="{{ url('/admin/payments_-check') }}" title="Back"><button class="btn btn-warning btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</button></a>
                        <a href="{{ url('/admin/payments_-check/' . $payments_check->id . '/edit') }}" title="Edit Payments_Check"><button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button></a>

                        <form method="POST" action="{{ url('admin/payments_check' . '/' . $payments_check->id) }}" accept-charset="UTF-8" style="display:inline">
                            {{ method_field('DELETE') }}
                            {{ csrf_field() }}
                            <button type="submit" class="btn btn-danger btn-sm" title="Delete Payments_Check" onclick="return confirm(&quot;Confirm delete?&quot;)"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</button>
                        </form>
                        <br/>
                        <br/>

                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <th>ID</th><td>{{ $payments_check->id }}</td>
                                    </tr>
                                    <tr><th> Uuid </th><td> {{ $payments_check->uuid }} </td></tr><tr><th> Create Date </th><td> {{ $payments_check->create_date }} </td></tr><tr><th> Subscription Uuid </th><td> {{ $payments_check->subscription_uuid }} </td></tr><tr><th> User Uuid </th><td> {{ $payments_check->user_uuid }} </td></tr><tr><th> User Id </th><td> {{ $payments_check->user_id }} </td></tr><tr><th> User Email </th><td> {{ $payments_check->user_email }} </td></tr><tr><th> Subscription Id </th><td> {{ $payments_check->subscription_id }} </td></tr><tr><th> Token </th><td> {{ $payments_check->token }} </td></tr><tr><th> Status </th><td> {{ $payments_check->status }} </td></tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>

@endsection
