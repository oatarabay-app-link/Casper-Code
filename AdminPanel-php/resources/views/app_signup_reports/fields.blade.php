<!-- User Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('user_id', 'User Id:') !!}
    {!! Form::number('user_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Email Field -->
<div class="form-group col-sm-6">
    {!! Form::label('email', 'Email:') !!}
    {!! Form::email('email', null, ['class' => 'form-control']) !!}
</div>

<!-- Status Field -->
<div class="form-group col-sm-6">
    {!! Form::label('status', 'Status:') !!}
    {!! Form::select('status', ['1' => 'App Signup', '2' => 'App Singedin', '3' => 'First Time Connection'], null, ['class' => 'form-control']) !!}
</div>

<!-- Signup Date Field -->
<div class="form-group col-sm-6">
    {!! Form::label('signup_date', 'Signup Date:') !!}
    {!! Form::text('signup_date', null, ['class' => 'form-control','id'=>'signup_date']) !!}
</div>

@push('scripts')
   <script type="text/javascript">
           $('#signup_date').datetimepicker({
               format: 'YYYY-MM-DD HH:mm:ss',
               useCurrent: true,
               icons: {
                   up: "icon-arrow-up-circle icons font-2xl",
                   down: "icon-arrow-down-circle icons font-2xl"
               },
               sideBySide: true
           })
       </script>
@endpush


<!-- Signedin Date Field -->
<div class="form-group col-sm-6">
    {!! Form::label('signedin_date', 'Signedin Date:') !!}
    {!! Form::text('signedin_date', null, ['class' => 'form-control','id'=>'signedin_date']) !!}
</div>

@push('scripts')
   <script type="text/javascript">
           $('#signedin_date').datetimepicker({
               format: 'YYYY-MM-DD HH:mm:ss',
               useCurrent: true,
               icons: {
                   up: "icon-arrow-up-circle icons font-2xl",
                   down: "icon-arrow-down-circle icons font-2xl"
               },
               sideBySide: true
           })
       </script>
@endpush


<!-- Subscription Field -->
<div class="form-group col-sm-6">
    {!! Form::label('subscription', 'Subscription:') !!}
    {!! Form::select('subscription', ['1' => 'Yes', '0' => 'No'], null, ['class' => 'form-control']) !!}
</div>

<!-- Emails Sent Field -->
<div class="form-group col-sm-6">
    {!! Form::label('emails_sent', 'Emails Sent:') !!}
    {!! Form::number('emails_sent', null, ['class' => 'form-control']) !!}
</div>

<!-- Emails Problems Field -->
<div class="form-group col-sm-6">
    {!! Form::label('emails_problems', 'Emails Problems:') !!}
    {!! Form::number('emails_problems', null, ['class' => 'form-control']) !!}
</div>

<!-- Device Field -->
<div class="form-group col-sm-6">
    {!! Form::label('device', 'Device:') !!}
    {!! Form::text('device', null, ['class' => 'form-control']) !!}
</div>

<!-- Country Field -->
<div class="form-group col-sm-6">
    {!! Form::label('Country', 'Country:') !!}
    {!! Form::text('Country', null, ['class' => 'form-control']) !!}
</div>

<!-- Os Field -->
<div class="form-group col-sm-6">
    {!! Form::label('OS', 'Os:') !!}
    {!! Form::text('OS', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{{ route('appSignupReports.index') }}" class="btn btn-secondary">Cancel</a>
</div>
