@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row m-t-30">
        <div class="col-sm-12 col-md-12 col-lg-4 col-xlg-4 m-b-30">
            <div class="widget-11 card no-margin">
                <div class="card-header">
                    <div class="card-title">
                        <h6 class="no-margin">Overall</h6>
                    </div>
                </div>
                <div class="widget-11-table auto-overflow">
                    <table class="table table-condensed table-hover">
                        <tbody>
                            <tr>
                                <td colspan="2" class="fs-12 b-r b-t b-dashed b-grey">
                                    <p class="m-b-0">Total Clients</p>
                                    <p class="hint-text small m-b-0">No. of Active Clients</p>
                                </td>
                                <td class="b-t b-t b-dashed b-grey">
                                    <h5 class="font-montserrat">{{ number_format($count_client) }}</h5>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" class="fs-12 b-r b-dashed b-grey">
                                    <p class="m-b-0">Total Payment Setup</p>
                                    <p class="hint-text small m-b-0">Payment Setup for Clients</p>
                                </td>
                                <td>
                                    <h5 class="font-montserrat">{{ number_format($count_setup) }}</h5>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" class="fs-12 b-r b-dashed b-grey">
                                    <p class="m-b-0">Current Pendings</p>
                                    <p class="hint-text small m-b-0">Pending Payments</p>
                                </td>
                                <td>
                                    <h5 class="font-montserrat">{{ number_format($count_entry) }}</h5>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" class="fs-12 b-r b-dashed b-grey">
                                    <p class="m-b-0">Total Payment Paid</p>
                                    <p class="hint-text small m-b-0">Payment History</p>
                                </td>
                                <td>
                                    <h5 class="font-montserrat">{{ number_format($count_detail) }}</h5>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" class="fs-12 b-r b-dashed b-grey">
                                    <p class="m-b-0">Total Payment Link</p>
                                    <p class="hint-text small m-b-0">Payment Link Sent</p>
                                </td>
                                <td>
                                    <h5 class="font-montserrat">{{ number_format($count_link_sent) }}</h5>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" class="fs-12 b-r b-dashed b-grey">
                                    <p class="m-b-0">Conversion Rate</p>
                                    <p class="hint-text small m-b-0">Link Sent Vs Payment Paid</p>
                                </td>
                                <td>
                                    <h5 class="font-montserrat">{{ $conversion_rate }}%</h5>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-md-12 col-lg-4 col-xlg-4 m-b-30">
            <div class="widget-11 card no-margin">
                <div class="card-header">
                    <div class="card-title">
                        <h6 class="no-margin">Payment Paid</h6>
                    </div>
                </div>
                <div class="widget-11-table auto-overflow">
                    <table class="table table-condensed table-hover">
                        <tbody>
                            <tr>
                                <td colspan="2" class="fs-12 b-r b-dashed b-grey">
                                    <p class="m-b-0">Today</p>
                                    <p class="hint-text small m-b-0">Payment Paid</p>
                                </td>
                                <td>
                                    <h5 class="font-montserrat">{{ number_format($count_today_paid) }}</h5>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" class="fs-12 b-r b-dashed b-grey">
                                    <p class="m-b-0">Last 7 days</p>
                                    <p class="hint-text small m-b-0">Payment Paid</p>
                                </td>
                                <td>
                                    <h5 class="font-montserrat">{{ number_format($count_last7days_paid) }}</h5>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" class="fs-12 b-r b-dashed b-grey">
                                    <p class="m-b-0">Last Month</p>
                                    <p class="hint-text small m-b-0">Payment Paid</p>
                                </td>
                                <td>
                                    <h5 class="font-montserrat">{{ number_format($count_last1month_paid) }}</h5>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" class="fs-12 b-r b-dashed b-grey">
                                    <p class="m-b-0">Last 3 Months</p>
                                    <p class="hint-text small m-b-0">Payment Paid</p>
                                </td>
                                <td>
                                    <h5 class="font-montserrat">{{ number_format($count_last3months_paid) }}</h5>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" class="fs-12 b-r b-dashed b-grey">
                                    <p class="m-b-0">Last 6 Months</p>
                                    <p class="hint-text small m-b-0">Payment Paid</p>
                                </td>
                                <td>
                                    <h5 class="font-montserrat">{{ number_format($count_last6months_paid) }}</h5>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" class="fs-12 b-r b-dashed b-grey">
                                    <p class="m-b-0">Last 12 Months</p>
                                    <p class="hint-text small m-b-0">Payment Paid</p>
                                </td>
                                <td>
                                    <h5 class="font-montserrat">{{ number_format($count_last12months_paid) }}</h5>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-md-12 col-lg-4 col-xlg-4 m-b-30">
            <div class="widget-11 card no-margin">
                <div class="card-header">
                    <div class="card-title">
                        <h6 class="no-margin">Payment Link Sent</h6>
                    </div>
                </div>
                <div class="widget-11-table auto-overflow">
                    <table class="table table-condensed table-hover">
                        <tbody>
                            <tr>
                                <td colspan="2" class="fs-12 b-r b-dashed b-grey">
                                    <p class="m-b-0">Today</p>
                                    <p class="hint-text small m-b-0">Payment Link Sent</p>
                                </td>
                                <td>
                                    <h5 class="font-montserrat">{{ number_format($count_today_sent) }}</h5>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" class="fs-12 b-r b-dashed b-grey">
                                    <p class="m-b-0">Last 7 days</p>
                                    <p class="hint-text small m-b-0">Payment Link Sent</p>
                                </td>
                                <td>
                                    <h5 class="font-montserrat">{{ number_format($count_last7days_sent) }}</h5>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" class="fs-12 b-r b-dashed b-grey">
                                    <p class="m-b-0">Last Month</p>
                                    <p class="hint-text small m-b-0">Payment Link Sent</p>
                                </td>
                                <td>
                                    <h5 class="font-montserrat">{{ number_format($count_last1month_sent) }}</h5>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" class="fs-12 b-r b-dashed b-grey">
                                    <p class="m-b-0">Last 3 Months</p>
                                    <p class="hint-text small m-b-0">Payment Link Sent</p>
                                </td>
                                <td>
                                    <h5 class="font-montserrat">{{ number_format($count_last3months_sent) }}</h5>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" class="fs-12 b-r b-dashed b-grey">
                                    <p class="m-b-0">Last 6 Months</p>
                                    <p class="hint-text small m-b-0">Payment Paid</p>
                                </td>
                                <td>
                                    <h5 class="font-montserrat">{{ number_format($count_last6months_sent) }}</h5>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" class="fs-12 b-r b-dashed b-grey">
                                    <p class="m-b-0">Last 12 Months</p>
                                    <p class="hint-text small m-b-0">Payment Paid</p>
                                </td>
                                <td>
                                    <h5 class="font-montserrat">{{ number_format($count_last12months_sent) }}</h5>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection