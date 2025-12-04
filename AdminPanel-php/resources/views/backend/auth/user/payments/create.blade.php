@extends('backend.auth.user.show')

@section("inner_content")
                <div class="card">
                    <div class="card-header">Create New Payment</div>
                    <div class="card-body">
                        <a href="{{ url('/admin/auth/user/'. $user->id .'/user-payments-logs') }}" title="Back"><button class="btn btn-warning btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</button></a>
                        <br />
                        <br />

                        @if ($errors->any())
                            <ul class="alert alert-danger">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif

                        <form method="POST" action="{{ url('/admin/auth/user/'. $user->id .'/user-payments-logs') }}" accept-charset="UTF-8" class="form-horizontal" enctype="multipart/form-data">
                            {{ csrf_field() }}

                            @include ('backend.auth.user.payments.form', ['formMode' => 'create'])

                        </form>

                    </div>
                </div>

@endsection
