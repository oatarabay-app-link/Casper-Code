@extends('backend.auth.user.show')

@section("inner_content")
    <div class="table-responsive">
        <table class="table">
            <thead>
            <tr>
                <th>#</th>
                <th>Subject</th>
                <th>Delivered At</th>
                <th>Process Status</th>
                <th>Status</th>




                <th>Total Opens</th>
                <th>Opens</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach($smtp2goemaildata as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->subject }}</td>
                    <td>{{ $item->delivered_at }}</td>
                    <td>{{ $item->process_status }}</td>
                    <td>{{ $item->status }}</td>


                    <td>{{ $item->total_opens }}</td>
                    <td>{{ $item->opens }}</td>
                    <td>
                        <a href="{{ url('/admin/s-m-t-p2-g-o-email-data/' . $item->id) }}"
                           title="View SMTP2GOEmailDatum">
                            <button class="btn btn-info btn-sm"><i class="fa fa-eye" aria-hidden="true"></i> View
                            </button>
                        </a>
                        <a href="{{ url('/admin/s-m-t-p2-g-o-email-data/' . $item->id . '/edit') }}"
                           title="Edit SMTP2GOEmailDatum">
                            <button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o"
                                                                      aria-hidden="true"></i> Edit
                            </button>
                        </a>

                        <form method="POST" action="{{ url('/admin/s-m-t-p2-g-o-email-data' . '/' . $item->id) }}"
                              accept-charset="UTF-8" style="display:inline">
                            {{ method_field('DELETE') }}
                            {{ csrf_field() }}
                            <button type="submit" class="btn btn-danger btn-sm" title="Delete SMTP2GOEmailDatum"
                                    onclick="return confirm(&quot;Confirm delete?&quot;)"><i class="fa fa-trash-o"
                                                                                             aria-hidden="true"></i>
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div
            class="pagination-wrapper"> {!! $smtp2goemaildata->appends(['search' => Request::get('search')])->render() !!} </div>
    </div>


@endsection
