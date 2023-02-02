@extends('layouts.app')

@section('title', 'System Settings')

@section('content')
<div class="container-fluid p-t-15">
    <div class="row">
        <div class="col-sm-12 col-md-12 col-lg-5 col-xlg-4">
            <h5>Settings for sending emails</h5>
            <form role="form" method="POST" action="{{ route('system.settings.save') }}">
                @csrf

                <div class="form-group required form-group-default @error('email_day') has-error @enderror">
                    <label>No of days to send before the ending of subscription</label>
                    <div class="controls">
                        <input type="text" class="form-control @error('email_day') error @enderror" name="email_day" placeholder="No. of Days" required autocomplete="off"
                        autofocus value="{{ old('email_day')?? $email_day }}">
                        @error('email_day')
                        <label class="error">{{ $message }}</label>
                        @enderror
                    </div>
                </div>

                <div class="form-group required form-group-default @error('extend_day') has-error @enderror">
                    <label>No of extended days to add after end of Subscription</label>
                    <div class="controls">
                        <input type="text" class="form-control @error('extend_day') error @enderror" name="extend_day"
                            placeholder="No. of Days" required autocomplete="off" value="{{ old('extend_day')?? $extend_day }}">
                        @error('extend_day')
                        <label class="error">{{ $message }}</label>
                        @enderror
                    </div>
                </div>

                <div class="form-group required form-group-default @error('email_send_time') has-error @enderror">
                    <label> Time to send email </label>
                    <div class="controls">
                        <input type="time" class="form-control @error('email_send_time') error @enderror" name="email_send_time"
                            placeholder="Time " required autocomplete="off" value="{{ old('email_send_time')?? $email_send_time }}">
                        @error('email_send_time')
                        <label class="error">{{ $message }}</label>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12">
                        <button class="btn btn-complete" type="submit">SAVE</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12">
                        <a href="{{ route('user.index') }}" class="btn btn-default m-t-10">GO BACK</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
