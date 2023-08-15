@extends('layouts.app')

@section('title', 'System Settings')

@section('content')
<div class="container-fluid p-t-15">
    @php
    $recurring = config('app.addons.type_recurring');
    @endphp
    <form role="form" method="POST" action="{{ route('settings.store') }}">
        @csrf

        <div class="card card-transparent">
            <div class="card-header ">
                <div class="card-title">Settings
                </div>
            </div>
            <div class="card-body no-padding">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card card-transparent flex-row">
                            <ul class="nav nav-tabs nav-tabs-simple nav-tabs-left bg-white col-2" id="tab-3">
                                <li class="nav-item">
                                    <a href="#" class="active" data-toggle="tab"
                                        data-target="#tab3hellowWorld">Weekly</a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" data-toggle="tab" data-target="#tab3FollowUs">Monthly</a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" data-toggle="tab" data-target="#tab3Inspire">Quarterly</a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" data-toggle="tab" data-target="#tab4Inspire">Yearly</a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" data-toggle="tab" data-target="#tab5Inspire">More</a>
                                </li>
                            </ul>
                            <div class="tab-content bg-white col-10">
                                <div class="tab-pane active" id="tab3hellowWorld">
                                    <div class="container-fluid">
                                        <div class="form-group row">
                                            <label for="email_before_end_weekly" class="col-md-5 control-label">No of
                                                days to send before the ending of
                                                subscription for Weekly Mail</label>
                                            <div class="col-md-7">
                                                <input type="number" class="form-control" id="email_before_end_weekly"
                                                    placeholder="Enter no. of Days"
                                                    name="data[no-of-days-to-send-before-the-ending-of-subscription-for-weekly-mail]"
                                                    value={{
                                                    $data['no-of-days-to-send-before-the-ending-of-subscription-for-weekly-mail']
                                                    }}>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="email_between_days_weekly" class="col-md-5 control-label">No of
                                                days to send email between days
                                                for Weekly Mail</label>
                                            <div class="col-md-7">
                                                <input type="number" class="form-control" id="email_between_days_weekly"
                                                    placeholder="Enter no. of Days"
                                                    name="data[no-of-days-to-send-email-between-days-for-weekly-mail]"
                                                    value={{
                                                    $data['no-of-days-to-send-email-between-days-for-weekly-mail'] }}>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="email_between_extend_days_weekly"
                                                class="col-md-5 control-label">No of days to send email
                                                between days for Extended Period for Weekly Mail</label>
                                            <div class="col-md-7">
                                                <input type="number" class="form-control"
                                                    id="email_between_extend_days_weekly"
                                                    placeholder="Enter no. of Days"
                                                    name="data[no-of-days-to-send-email-between-days-for-extended-period-for-weekly-mail]"
                                                    value={{
                                                    $data['no-of-days-to-send-email-between-days-for-extended-period-for-weekly-mail']
                                                    }}>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="email_send_time_weekly" class="col-md-5 control-label">Time to
                                                send E-Mails for Weekly
                                                Mail</label>
                                            <div class="col-md-7">
                                                <input type="time" class="form-control" id="email_send_time_weekly"
                                                    placeholder="Enter time"
                                                    name="data[time-to-send-e-mails-for-weekly-mail]" value={{
                                                    $data['time-to-send-e-mails-for-weekly-mail'] }}>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="tab-pane" id="tab3FollowUs">
                                    <div class="container-fluid">
                                        <div class="form-group row">
                                            <label for="email_before_end_monthly" class="col-md-5 control-label">No of
                                                days to send before the ending of
                                                subscription for Monthly Mail</label>
                                            <div class="col-md-7">
                                                <input type="number" class="form-control" id="email_before_end_monthly"
                                                    placeholder="Enter no. of Days"
                                                    name="data[no-of-days-to-send-before-the-ending-of-subscription-for-monthly-mail]"
                                                    value={{
                                                    $data['no-of-days-to-send-before-the-ending-of-subscription-for-monthly-mail']
                                                    }}>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="email_between_days_monthly" class="col-md-5 control-label">No of
                                                days to send email between days
                                                for Monthly Mail</label>
                                            <div class="col-md-7">
                                                <input type="number" class="form-control" id="email_between_days_monthly"
                                                    placeholder="Enter no. of Days"
                                                    name="data[no-of-days-to-send-email-between-days-for-monthly-mail]"
                                                    value={{
                                                    $data['no-of-days-to-send-email-between-days-for-monthly-mail'] }}>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="email_between_extend_days_monthly"
                                                class="col-md-5 control-label">No of days to send email
                                                between days for Extended Period for Monthly Mail</label>
                                            <div class="col-md-7">
                                                <input type="number" class="form-control"
                                                    id="email_between_extend_days_monthly"
                                                    placeholder="Enter no. of Days"
                                                    name="data[no-of-days-to-send-email-between-days-for-extended-period-for-monthly-mail]"
                                                    value={{
                                                    $data['no-of-days-to-send-email-between-days-for-extended-period-for-monthly-mail']
                                                    }}>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="email_send_time_monthly" class="col-md-5 control-label">Time to
                                                send E-Mails for Monthly
                                                Mail</label>
                                            <div class="col-md-7">
                                                <input type="time" class="form-control" id="email_send_time_monthly"
                                                    placeholder="Enter time"
                                                    name="data[time-to-send-e-mails-for-monthly-mail]" value={{
                                                    $data['time-to-send-e-mails-for-monthly-mail'] }}>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="tab3Inspire">
                                    <div class="container-fluid">
                                        <div class="form-group row">
                                            <label for="email_before_end_quarterly" class="col-md-5 control-label">No of
                                                days to send before the ending of
                                                subscription for Quarterly Mail</label>
                                            <div class="col-md-7">
                                                <input type="number" class="form-control" id="email_before_end_quarterly"
                                                    placeholder="Enter no. of Days"
                                                    name="data[no-of-days-to-send-before-the-ending-of-subscription-for-quarterly-mail]"
                                                    value={{
                                                    $data['no-of-days-to-send-before-the-ending-of-subscription-for-quarterly-mail']
                                                    }}>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="email_between_days_quarterly" class="col-md-5 control-label">No
                                                of
                                                days to send email between days
                                                for Quarterly Mail</label>
                                            <div class="col-md-7">
                                                <input type="number" class="form-control"
                                                    id="email_between_days_quarterly" placeholder="Enter no. of Days"
                                                    name="data[no-of-days-to-send-email-between-days-for-quarterly-mail]"
                                                    value={{
                                                    $data['no-of-days-to-send-email-between-days-for-quarterly-mail']
                                                    }}>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="email_between_extend_days_quarterly"
                                                class="col-md-5 control-label">No of days to send email
                                                between days for Extended Period for Quarterly Mail</label>
                                            <div class="col-md-7">
                                                <input type="number" class="form-control"
                                                    id="email_between_extend_days_quarterly"
                                                    placeholder="Enter no. of Days"
                                                    name="data[no-of-days-to-send-email-between-days-for-extended-period-for-quarterly-mail]"
                                                    value={{
                                                    $data['no-of-days-to-send-email-between-days-for-extended-period-for-quarterly-mail']
                                                    }}>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="email_send_time_quarterly" class="col-md-5 control-label">Time
                                                to
                                                send E-Mails for Quarterly
                                                Mail</label>
                                            <div class="col-md-7">
                                                <input type="time" class="form-control" id="email_send_time_quarterly"
                                                    placeholder="Enter time"
                                                    name="data[time-to-send-e-mails-for-quarterly-mail]" value={{
                                                    $data['time-to-send-e-mails-for-quarterly-mail'] }}>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="tab4Inspire">
                                    <div class="container-fluid">
                                        <div class="form-group row">
                                            <label for="email_before_end_yearly" class="col-md-5 control-label">No of
                                                days to send before the ending of
                                                subscription for Yearly Mail</label>
                                            <div class="col-md-7">
                                                <input type="number" class="form-control" id="email_before_end_yearly"
                                                    placeholder="Enter no. of Days"
                                                    name="data[no-of-days-to-send-before-the-ending-of-subscription-for-yearly-mail]"
                                                    value={{
                                                    $data['no-of-days-to-send-before-the-ending-of-subscription-for-yearly-mail']
                                                    }}>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="email_between_days_yearly" class="col-md-5 control-label">No of
                                                days to send email between days
                                                for Yearly Mail</label>
                                            <div class="col-md-7">
                                                <input type="number" class="form-control" id="email_between_days_yearly"
                                                    placeholder="Enter no. of Days"
                                                    name="data[no-of-days-to-send-email-between-days-for-yearly-mail]"
                                                    value={{
                                                    $data['no-of-days-to-send-email-between-days-for-yearly-mail'] }}>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="email_between_extend_days_yearly"
                                                class="col-md-5 control-label">No of days to send email
                                                between days for Extended Period for Yearly Mail</label>
                                            <div class="col-md-7">
                                                <input type="number" class="form-control"
                                                    id="email_between_extend_days_yearly"
                                                    placeholder="Enter no. of Days"
                                                    name="data[no-of-days-to-send-email-between-days-for-extended-period-for-yearly-mail]"
                                                    value={{
                                                    $data['no-of-days-to-send-email-between-days-for-extended-period-for-yearly-mail']
                                                    }}>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="email_send_time_yearly" class="col-md-5 control-label">Time to
                                                send E-Mails for Yearly
                                                Mail</label>
                                            <div class="col-md-7">
                                                <input type="time" class="form-control" id="email_send_time_yearly"
                                                    placeholder="Enter time"
                                                    name="data[time-to-send-e-mails-for-yearly-mail]" value={{
                                                    $data['time-to-send-e-mails-for-yearly-mail'] }}>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="tab5Inspire">
                                    <div class="container-fluid">
                                        <div class="form-group row">
                                            <label for="vat_rate" class="col-md-5 control-label">VAT Rate(%)</label>
                                            <div class="col-md-7">
                                                <input type="number" class="form-control" id="vat_rate"
                                                    placeholder="Enter Rate in Number"
                                                    name="data[vat-rate]" value={{
                                                    $data['vat-rate'] }}>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
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