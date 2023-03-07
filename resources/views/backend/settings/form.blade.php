@extends('layouts.app')

@section('title', 'System Settings')

@section('content')
<div class="container-fluid p-t-15">
    <h5>Settings for sending emails</h5>
    @php
    $recurring = config('app.addons.type_recurring');
    @endphp
    <form role="form" method="POST" action="{{ route('system.settings.save') }}">
        @csrf
        <h5>FOR WEEKLY SETTINGS</h5>
        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-6 col-xlg-6">
                <div class="form-group required form-group-default">
                    <label>No of days to send before the ending of subscription</label>
                    <div class="controls">
                        <input type="text" class="form-control" name="email_day[{{ $recurring['WEEKLY'] }}]"
                            placeholder="No. of Days" required autocomplete="off" autofocus
                            value="{{ isset($email_day[ $recurring['WEEKLY']])?$email_day[ $recurring['WEEKLY']]:0}}">
                        @error('email_day')
                        <label class="error">{{ $message }}</label>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-12 col-lg-6 col-xlg-6">
                <div class="form-group required form-group-default">
                    <label>No of days to send email between days</label>
                    <div class="controls">
                        <input type="text" class="form-control" name="days_between_mail[{{ $recurring['WEEKLY'] }}]"
                            placeholder="No. of Days" required autocomplete="off" autofocus
                            value="{{ isset($days_between_mail[ $recurring['WEEKLY']])?$days_between_mail[ $recurring['WEEKLY']]:0}}">
                        @error('email_day')
                        <label class="error">{{ $message }}</label>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-6 col-xlg-6">
                <div class="form-group required form-group-default">
                    <label>No of days to send email between days for Extended Period</label>
                    <div class="controls">
                        <input type="text" class="form-control"
                            name="days_between_extended_mail[{{ $recurring['WEEKLY'] }}]" placeholder="No. of Days"
                            required autocomplete="off" autofocus
                            value="{{ isset($days_between_extended_mail[ $recurring['WEEKLY']])?$days_between_extended_mail[ $recurring['WEEKLY']]:0}}">
                        @error('email_day')
                        <label class="error">{{ $message }}</label>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-12 col-lg-6 col-xlg-6">
                <div class="form-group required form-group-default">
                    <label>Time to send E-Mails</label>
                    <div class="controls">
                        <input type="time" class="form-control" name="send_email_time[{{ $recurring['WEEKLY'] }}]"
                            placeholder="No. of Days" required autocomplete="off" autofocus
                            value="{{ isset($send_email_time[ $recurring['WEEKLY']])?$send_email_time[ $recurring['WEEKLY']]:0}}">
                        @error('email_day')
                        <label class="error">{{ $message }}</label>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
        <h5>FOR MONTHLY SETTINGS</h5>
        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-6 col-xlg-6">
                <div class="form-group required form-group-default">
                    <label>No of days to send before the ending of subscription</label>
                    <div class="controls">
                        <input type="text" class="form-control" name="email_day[{{ $recurring['MONTHLY'] }}]"
                            placeholder="No. of Days" required autocomplete="off" autofocus
                            value="{{ isset($email_day[ $recurring['MONTHLY']])?$email_day[ $recurring['MONTHLY']]:0}}">
                        @error('email_day')
                        <label class="error">{{ $message }}</label>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-12 col-lg-6 col-xlg-6">
                <div class="form-group required form-group-default">
                    <label>No of days to send email between days</label>
                    <div class="controls">
                        <input type="text" class="form-control" name="days_between_mail[{{ $recurring['MONTHLY'] }}]"
                            placeholder="No. of Days" required autocomplete="off" autofocus
                            value="{{ isset($days_between_mail[ $recurring['MONTHLY']])?$days_between_mail[ $recurring['MONTHLY']]:0}}">
                        @error('email_day')
                        <label class="error">{{ $message }}</label>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-6 col-xlg-6">
                <div class="form-group required form-group-default">
                    <label>No of days to send email between days for Extended Period</label>
                    <div class="controls">
                        <input type="text" class="form-control"
                            name="days_between_extended_mail[{{ $recurring['MONTHLY'] }}]" placeholder="No. of Days"
                            required autocomplete="off" autofocus
                            value="{{ isset($days_between_extended_mail[ $recurring['MONTHLY']])?$days_between_extended_mail[ $recurring['MONTHLY']]:0}}">
                        @error('email_day')
                        <label class="error">{{ $message }}</label>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-12 col-lg-6 col-xlg-6">
                <div class="form-group required form-group-default">
                    <label>Time to send E-Mails</label>
                    <div class="controls">
                        <input type="time" class="form-control" name="send_email_time[{{ $recurring['MONTHLY'] }}]"
                            placeholder="No. of Days" required autocomplete="off" autofocus
                            value="{{ isset($send_email_time[ $recurring['MONTHLY']])?$send_email_time[ $recurring['MONTHLY']]:0}}">
                        @error('email_day')
                        <label class="error">{{ $message }}</label>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <h5>FOR QUARTERLY SETTINGS</h5>
        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-6 col-xlg-6">
                <div class="form-group required form-group-default">
                    <label>No of days to send before the ending of subscription</label>
                    <div class="controls">
                        <input type="text" class="form-control" name="email_day[{{ $recurring['QUARTERLY'] }}]"
                            placeholder="No. of Days" required autocomplete="off" autofocus
                            value="{{ isset($email_day[ $recurring['QUARTERLY']])?$email_day[ $recurring['QUARTERLY']]:0}}">
                        @error('email_day')
                        <label class="error">{{ $message }}</label>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-12 col-lg-6 col-xlg-6">
                <div class="form-group required form-group-default">
                    <label>No of days to send email between days</label>
                    <div class="controls">
                        <input type="text" class="form-control" name="days_between_mail[{{ $recurring['QUARTERLY'] }}]"
                            placeholder="No. of Days" required autocomplete="off" autofocus
                            value="{{ isset($days_between_mail[ $recurring['QUARTERLY']])?$days_between_mail[ $recurring['QUARTERLY']]:0}}">
                        @error('email_day')
                        <label class="error">{{ $message }}</label>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-6 col-xlg-6">
                <div class="form-group required form-group-default">
                    <label>No of days to send email between days for Extended Period</label>
                    <div class="controls">
                        <input type="text" class="form-control"
                            name="days_between_extended_mail[{{ $recurring['QUARTERLY'] }}]" placeholder="No. of Days"
                            required autocomplete="off" autofocus
                            value="{{ isset($days_between_extended_mail[ $recurring['QUARTERLY']])?$days_between_extended_mail[ $recurring['QUARTERLY']]:0}}">
                        @error('email_day')
                        <label class="error">{{ $message }}</label>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-12 col-lg-6 col-xlg-6">
                <div class="form-group required form-group-default">
                    <label>Time to send E-Mails</label>
                    <div class="controls">
                        <input type="time" class="form-control" name="send_email_time[{{ $recurring['QUARTERLY'] }}]"
                            placeholder="No. of Days" required autocomplete="off" autofocus
                            value="{{ isset($send_email_time[ $recurring['QUARTERLY']])?$send_email_time[ $recurring['QUARTERLY']]:0}}">
                        @error('email_day')
                        <label class="error">{{ $message }}</label>
                        @enderror
                    </div>
                </div>
            </div>
        </div>


        <h5>FOR YEARLY SETTINGS</h5>
        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-6 col-xlg-6">
                <div class="form-group required form-group-default">
                    <label>No of days to send before the ending of subscription</label>
                    <div class="controls">
                        <input type="text" class="form-control" name="email_day[{{ $recurring['YEARLY'] }}]"
                            placeholder="No. of Days" required autocomplete="off" autofocus
                            value="{{ isset($email_day[ $recurring['YEARLY']])?$email_day[ $recurring['YEARLY']]:0}}">
                        @error('email_day')
                        <label class="error">{{ $message }}</label>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-12 col-lg-6 col-xlg-6">
                <div class="form-group required form-group-default">
                    <label>No of days to send email between days</label>
                    <div class="controls">
                        <input type="text" class="form-control" name="days_between_mail[{{ $recurring['YEARLY'] }}]"
                            placeholder="No. of Days" required autocomplete="off" autofocus
                            value="{{ isset($days_between_mail[ $recurring['YEARLY']])?$days_between_mail[ $recurring['YEARLY']]:0}}">
                        @error('email_day')
                        <label class="error">{{ $message }}</label>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-6 col-xlg-6">
                <div class="form-group required form-group-default">
                    <label>No of days to send email between days for Extended Period</label>
                    <div class="controls">
                        <input type="text" class="form-control"
                            name="days_between_extended_mail[{{ $recurring['YEARLY'] }}]" placeholder="No. of Days"
                            required autocomplete="off" autofocus
                            value="{{ isset($days_between_extended_mail[ $recurring['YEARLY']])?$days_between_extended_mail[ $recurring['YEARLY']]:0}}">
                        @error('email_day')
                        <label class="error">{{ $message }}</label>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-12 col-lg-6 col-xlg-6">
                <div class="form-group required form-group-default">
                    <label>Time to send E-Mails</label>
                    <div class="controls">
                        <input type="time" class="form-control" name="send_email_time[{{ $recurring['YEARLY'] }}]"
                            placeholder="No. of Days" required autocomplete="off" autofocus
                            value="{{ isset($send_email_time[ $recurring['YEARLY']])?$send_email_time[ $recurring['YEARLY']]:0}}">
                        @error('email_day')
                        <label class="error">{{ $message }}</label>
                        @enderror
                    </div>
                </div>
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
@endsection