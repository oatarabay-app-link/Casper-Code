@extends('backend.layouts.app')

@section('content')
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">SMTP2GO EMAIL DATA</div>
            <div class="card-body">
                        <a href="{{ url('/admin/s-m-t-p2-g-o-email-data/create') }}" class="btn btn-success btn-sm" title="Add New SMTP2GOEmailDatum">
                            <i class="fa fa-plus" aria-hidden="true"></i> Add New
                        </a>

                        <form method="GET" action="{{ url('/admin/s-m-t-p2-g-o-email-data') }}" accept-charset="UTF-8" class="form-inline my-2 my-lg-0 float-right" role="search">
                            <div class="input-group">
                                <input type="text" class="form-control" name="search" placeholder="Search..." value="{{ request('search') }}">
                                <span class="input-group-append">
                                    <button class="btn btn-secondary" type="submit">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </span>
                            </div>
                        </form>

                        <br/>
                        <br/>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>#</th><th>Subject</th><th>Delivered At</th><th>Process Status</th><th>Email Id</th><th>Status</th><th>Response</th><th>Email Tx</th><th>Host</th><th>Smtpcode</th><th>Sender</th><th>Recipient</th><th>Stmp2gousername</th><th>Headers</th><th>Total Opens</th><th>Opens</th><th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($smtp2goemaildata as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->subject }}</td><td>{{ $item->delivered_at }}</td><td>{{ $item->process_status }}</td><td>{{ $item->email_id }}</td><td>{{ $item->status }}</td><td>{{ $item->response }}</td><td>{{ $item->email_tx }}</td><td>{{ $item->host }}</td><td>{{ $item->smtpcode }}</td><td>{{ $item->sender }}</td><td>{{ $item->recipient }}</td><td>{{ $item->stmp2gousername }}</td><td>{{ $item->headers }}</td><td>{{ $item->total_opens }}</td><td>{{ $item->opens }}</td>
                                        <td>
                                            <a href="{{ url('/admin/s-m-t-p2-g-o-email-data/' . $item->id) }}" title="View SMTP2GOEmailDatum"><button class="btn btn-info btn-sm"><i class="fa fa-eye" aria-hidden="true"></i> View</button></a>
                                            <a href="{{ url('/admin/s-m-t-p2-g-o-email-data/' . $item->id . '/edit') }}" title="Edit SMTP2GOEmailDatum"><button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button></a>

                                            <form method="POST" action="{{ url('/admin/s-m-t-p2-g-o-email-data' . '/' . $item->id) }}" accept-charset="UTF-8" style="display:inline">
                                                {{ method_field('DELETE') }}
                                                {{ csrf_field() }}
                                                <button type="submit" class="btn btn-danger btn-sm" title="Delete SMTP2GOEmailDatum" onclick="return confirm(&quot;Confirm delete?&quot;)"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <div class="pagination-wrapper"> {!! $smtp2goemaildata->appends(['search' => Request::get('search')])->render() !!} </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
