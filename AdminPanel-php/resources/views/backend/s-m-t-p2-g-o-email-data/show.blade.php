@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            @include('admin.sidebar')

            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">SMTP2GOEmailDatum {{ $smtp2goemaildatum->id }}</div>
                    <div class="card-body">

                        <a href="{{ url('/admin/s-m-t-p2-g-o-email-data') }}" title="Back"><button class="btn btn-warning btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</button></a>
                        <a href="{{ url('/admin/s-m-t-p2-g-o-email-data/' . $smtp2goemaildatum->id . '/edit') }}" title="Edit SMTP2GOEmailDatum"><button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button></a>

                        <form method="POST" action="{{ url('admin/smtp2goemaildata' . '/' . $smtp2goemaildatum->id) }}" accept-charset="UTF-8" style="display:inline">
                            {{ method_field('DELETE') }}
                            {{ csrf_field() }}
                            <button type="submit" class="btn btn-danger btn-sm" title="Delete SMTP2GOEmailDatum" onclick="return confirm(&quot;Confirm delete?&quot;)"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</button>
                        </form>
                        <br/>
                        <br/>

                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <th>ID</th><td>{{ $smtp2goemaildatum->id }}</td>
                                    </tr>
                                    <tr><th> Subject </th><td> {{ $smtp2goemaildatum->subject }} </td></tr><tr><th> Delivered At </th><td> {{ $smtp2goemaildatum->delivered_at }} </td></tr><tr><th> Process Status </th><td> {{ $smtp2goemaildatum->process_status }} </td></tr><tr><th> Email Id </th><td> {{ $smtp2goemaildatum->email_id }} </td></tr><tr><th> Status </th><td> {{ $smtp2goemaildatum->status }} </td></tr><tr><th> Response </th><td> {{ $smtp2goemaildatum->response }} </td></tr><tr><th> Email Tx </th><td> {{ $smtp2goemaildatum->email_tx }} </td></tr><tr><th> Host </th><td> {{ $smtp2goemaildatum->host }} </td></tr><tr><th> Smtpcode </th><td> {{ $smtp2goemaildatum->smtpcode }} </td></tr><tr><th> Sender </th><td> {{ $smtp2goemaildatum->sender }} </td></tr><tr><th> Recipient </th><td> {{ $smtp2goemaildatum->recipient }} </td></tr><tr><th> Stmp2gousername </th><td> {{ $smtp2goemaildatum->stmp2gousername }} </td></tr><tr><th> Headers </th><td> {{ $smtp2goemaildatum->headers }} </td></tr><tr><th> Total Opens </th><td> {{ $smtp2goemaildatum->total_opens }} </td></tr><tr><th> Opens </th><td> {{ $smtp2goemaildatum->opens }} </td></tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
