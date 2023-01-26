@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
@php 
$payment_type = config('app.addons.payment_type'); 
$payment_status = config('app.addons.payment_status'); 
$ref_code_prefix = config('app.addons.ref_code_prefix');
@endphp
<div class="container-fluid">
    <div class="row m-t-30">
        <div class="col-sm-12 col-md-12 col-lg-12">
            <div class="card m-b-15">
                <div class="card-header">
                    <div class="card-title full-width">
                        <h5 class="no-margin">Pending Payments
                            <a href="{{ route('payment.create') }}" class="btn btn-info pull-right m-r-5">Create Payment</a>
                        </h5>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-hover table-responsive-block dataTable with-export custom-table">
                        <thead>
                            <tr>
                                <th width="15">#</th>
                                <th width="75">Ref Code</th>
                                <th width="50">Client</th>
                                <th width="50">Email</th>
                                <th width="50">Amount</th>
                                <th width="75">Payment Status</th>
                                <th width="75">Link Status</th>
                                <th width="100">Remarks</th>
                                <th width="50">Created Date</th>
                                <th width="100">Option</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($data != null)
                                @php $i = 0; @endphp
                                @foreach ($data as $row)
                                @php $i++; $current_timestamp = time(); @endphp
                                <tr>
                                    <td>{{ $i }}</td>
                                    <td>{{ $ref_code_prefix . '-' . $row->ref_code }}</td>
                                    <td>{{ $row->client->name }}</td>
                                    <td>{{ $row->email }}</td>
                                    <td>{{ $row->currency . ' ' . number_format($row->total, 2) }}</td>
                                    <td>
                                        @if ($row->payment_type != null)
                                            <strong class="text-success">{{ $row->payment_type }} : </strong>
                                        @endif
                                        @if ($row->payment_status != null)
                                            <strong class="{{ $row->payment_status == $status_payment['COMPLETED'] ? 'text-success' : 'text-danger' }}">
                                                {{ $payment_status[$row->payment_status] }}
                                            </strong>
                                        @else
                                            <strong class="text-danger">PENDING</strong>
                                        @endif
                                    </td>
                                    <td>
                                        @if (strtotime($row->expired_at) > $current_timestamp && $row->is_active == 10)
                                            <strong class="text-success">ACTIVE <br/> EXPIRY {{ $row->expired_at }}</strong>
                                        @else
                                            <strong class="text-danger">INACTIVE <br/> EXPIRY {{ $row->expired_at }}</strong>
                                        @endif
                                    </td>
                                    <td>{{ $row->remarks }}</td>
                                    <td>{{ $row->created_at }}</td>
                                    <td>
                                        <a href="{{ route('payment.edit', [$row->uuid]) }}" class="btn btn-info m-b-5">EDIT</a>
                                        @if (strtotime($row->expired_at) > $current_timestamp)
                                        <button class="btn {{ $row->is_active == 10 ? 'btn-danger' : 'btn-success' }} m-b-5 btn-change-status" type="button" data-index="{{ $i }}">
                                            <span>{{ $row->is_active == 10 ? 'DEACTIVATE' : 'ACTIVATE' }}</span>
                                        </button>
                                        <form action="{{ route('payment.change.status', [$row->uuid]) }}" method="POST" class="change-status-form-{{ $i }}" style="display: none;">@csrf @method('PUT')</form>
                                        @endif
                                        
                                        @if (strtotime($row->expired_at) > $current_timestamp && $row->is_active == 10)
                                        <button class="btn btn-primary m-b-5 btn-proceed-init" data-url="{{ route('payment.copy', [$row->uuid]) }}" type="button">COPY LINK</button>

                                        <button class="btn btn-complete m-b-5 btn-proceed-init" data-url="{{ route('payment.send', [$row->uuid]) }}" type="button">SEND EMAIL</button>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade stick-up show" id="master-auth-modal" data-backdrop="static" data-keyboard="false" style="padding: 0 !important;">
    <div class="modal-dialog" style="max-width: 425px; margin: 0 auto;">
        <div class="modal-content">
            <div class="modal-header clearfix text-left">
                <h5>Enter Your Master Password</h5>
                <p>Please verify your access before proceeding</p>
            </div>
            <div class="modal-body">
                <form action="" method="POST" id="master-auth-form">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group form-group-default">
                                <label>Enter Your Master Password</label>
                                <input type="password" class="form-control" required autofocus placeholder="Password" autocomplete="off" name="master_password" id="master-auth-password">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-lg btn-primary btn-block m-t-5 btn-proceed">PROCEED</button>
                        </div>
                        <div class="col-md-6">
                            <button type="button" class="btn btn-lg btn-danger btn-block m-t-5" data-dismiss="modal">CANCEL</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-specific-style')
<link rel="stylesheet" href="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.css') }}">
<link href="{{ asset('assets/plugins/table/css/dataTables.bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/plugins/table/css/buttons.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/plugins/table/css/dataTables.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('page-specific-script')
<script src="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/plugins/table/js/jquery.dataTables.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/plugins/table/js/dataTables.fixedColumns.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/plugins/table/js/dataTables.buttons.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/plugins/table/js/jszip.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/plugins/table/js/pdfmake.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/plugins/table/js/vfs_fonts.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/plugins/table/js/buttons.html5.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/js/table.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/js/master.auth.min.js') }}" type="text/javascript"></script>
@endsection