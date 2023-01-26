@extends('layouts.app')

@section('title', 'Payments')

@section('content')
@php 
$payment_status = config('app.addons.payment_status');
$status_payment = config('app.addons.status_payment'); 
$ref_code_prefix = config('app.addons.ref_code_prefix');
@endphp
<div class="container-fluid">
    <div class="row m-t-30">
        <div class="col-sm-12 col-md-12 col-lg-12">
            <div class="card m-b-15">
                <div class="card-header">
                    <div class="card-title full-width">
                        <h5 class="no-margin">Payments
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
                                            <strong class="text-complete">{{ $row->payment_type }}</strong>
                                        @endif
                                        @if ($row->payment_status != null)
                                            <br/><strong class="{{ $row->payment_status == $status_payment['COMPLETED'] ? 'text-success' : 'text-danger' }}">{{ $payment_status[$row->payment_status] }}</strong>

                                            @if ($row->payment_status == $status_payment['REFUNDED'])
                                                <br/><strong class="text-danger">{{ $row->updated_at }}</strong>
                                            @endif
                                        @else
                                            <strong class="text-danger">PENDING</strong>
                                        @endif
                                    </td>
                                    <td>
                                        @if (strtotime($row->expired_at) > $current_timestamp && $row->is_active == 10)
                                            <strong class="text-success">ACTIVE <br/> EXPIRY <br/>{{ $row->expired_at }}</strong>
                                        @else
                                            <strong class="text-danger">INACTIVE <br/> EXPIRY <br/>{{ $row->expired_at }}</strong>
                                        @endif
                                    </td>
                                    <td>{{ $row->remarks }}</td>
                                    <td>{{ $row->created_at }}</td>
                                    <td class="list-item">
                                        <button class="btn btn-primary m-b-5 btn-view" type="button">VIEW</button>
                                        @if ($row->payment_status == $status_payment['PENDING'])
                                            <a href="{{ route('payment.edit', [$row->uuid]) }}" class="btn btn-info m-b-5">EDIT</a>
                                            @if (strtotime($row->expired_at) > $current_timestamp)
                                            <button class="btn {{ $row->is_active == 10 ? 'btn-danger' : 'btn-success' }} m-b-5 btn-change-status" type="button" data-index="{{ $i }}">
                                                <span>{{ $row->is_active == 10 ? 'DEACTIVATE' : 'ACTIVATE' }}</span>
                                            </button>
                                            <form action="{{ route('payment.change.status', [$row->uuid]) }}" method="POST" class="change-status-form-{{ $i }}" style="display: none;">@csrf @method('PUT')</form>
                                            @endif
                                            
                                            @if (strtotime($row->expired_at) > $current_timestamp && $row->is_active == 10)
                                            <button class="btn btn-complete m-b-5 btn-proceed-init" data-url="{{ route('payment.copy', [$row->uuid]) }}" type="button">COPY LINK</button>

                                            <button class="btn btn-complete m-b-5 btn-proceed-init" data-url="{{ route('payment.send', [$row->uuid]) }}" type="button">SEND EMAIL</button>
                                            @endif
                                        @else
                                            <a href="{{ route('payment.edit', [$row->uuid]) }}" class="btn btn-info m-b-5">NEW</a>
                                            <button class="btn btn-complete m-b-5 btn-show-transaction" data-url="{{ route('payment.transaction', [$row->uuid]) }}" data-ref="{{ $ref_code_prefix . '-' . $row->ref_code }}" data-type="{{ $row->payment_type }}" data-status="{{ $payment_status[$row->payment_status] }}" type="button">SHOW DETAIL</button>

                                            @if ($row->payment_status == $status_payment['COMPLETED'])
                                                @if ($row->payment_type == 'NIBL')
                                                    <button class="btn btn-danger m-b-5 btn-proceed-init-refund" data-url="{{ route('payment.refund', [$row->uuid]) }}" data-ref="{{ $ref_code_prefix . '-' . $row->ref_code }}" data-currency="{{ $row->currency }}" data-total="{{ $row->total }}" type="button">REFUND PAYMENT</button>
                                                @endif
                                            @endif
                                        @endif

                                        <input type="hidden" value="{{ $ref_code_prefix . '-' . $row->ref_code }}" class="payment-ref-code">
                                        <input type="hidden" value='{{ $row->client->name }}' class="payment-client">
                                        <input type="hidden" value='{{ $row->email }}' class="payment-email">
                                        <input type="hidden" value="{{ $row->currency . ' ' . number_format($row->total, 2) }}" class="payment-total">
                                        <input type="hidden" value='{{ $row->remarks }}' class="payment-remarks">
                                        <input type="hidden" value='{{ $row->created_at }}' class="payment-created">
                                        <input type="hidden" value='{{ $row->is_active == 10 ? "ACTIVE" : "INACTIVE" }}' class="payment-active">
                                        <input type="hidden" value='{{ $row->expired_at }}' class="payment-expired">
                                        <input type="hidden" value='{{ $row->contents }}' class="payment-contents">
                                        <input type="hidden" value='{{ $row->payment_options }}' class="payment-options">
                                        <input type="hidden" value='{{ $row->payment_type }}' class="payment-type">
                                        <input type="hidden" value='{{ $row->payment_status ? $payment_status[$row->payment_status] : "" }}' class="payment-status">
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
    <div class="modal-dialog modal-sm" style="max-width: 425px; margin: 0 auto;">
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
                            <button type="submit" class="btn btn-lg btn-primary btn-block m-t-5 btn-proceed">Proceed</button>
                        </div>
                        <div class="col-md-6">
                            <button type="button" class="btn btn-lg btn-danger btn-block m-t-5" data-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade stick-up show" id="master-auth-refund-modal" data-backdrop="static" data-keyboard="false" style="padding: 0 !important;">
    <div class="modal-dialog modal-sm" style="max-width: 425px; margin: 0 auto;">
        <div class="modal-content">
            <div class="modal-header clearfix text-left">
                <h5>Enter Your Master Password</h5>
                <p>Please verify your access before proceeding</p>
            </div>
            <div class="modal-body">
                <form action="" method="POST" id="master-auth-refund-form">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group form-group-default input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Ref#</span>
                                </div>
                                <div class="form-input-group">
                                    <label>Payment Reference Code</label>
                                    <input type="text" class="form-control" readonly id="refund-ref-code">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group form-group-default input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text refund-currency"></span>
                                </div>
                                <div class="form-input-group">
                                    <label>Paid Amount</label>
                                    <input type="text" class="form-control" readonly id="refund-total">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group form-group-default form-check-group">
                                <div class="form-check switch switch-lg danger full-width right m-b-15 m-t-15">
                                    <input type="checkbox" name="is_full" value="10" id="refund-full" checked>
                                <label for="refund-full">Full Refund ?</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group form-group-default input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text refund-currency"></span>
                                </div>
                                <div class="form-input-group">
                                    <label>Refund Amount</label>
                                    <input type="text" class="form-control" required placeholder="Refund Amount" autocomplete="off" name="refund_amount" id="refund-amount" disabled>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row m-t-5">
                        <div class="col-md-12">
                            <div class="form-group form-group-default">
                                <label>Enter Your Master Password</label>
                                <input type="password" class="form-control" required autofocus placeholder="Password" autocomplete="off" name="master_password" id="master-auth-refund-password">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-lg btn-primary btn-block m-t-5 btn-proceed-refund">Proceed</button>
                        </div>
                        <div class="col-md-6">
                            <button type="button" class="btn btn-lg btn-danger btn-block m-t-5" data-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade slide-right show" id="show-transaction-modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-sm" style="max-width: 425px; margin: 0 auto;">
        <div class="modal-content-wrapper">
            <div class="modal-content">
                <div class="modal-header clearfix text-left">
                    <h5>Transaction Detail</h5>
                    <p>Ref# <strong class="transaction-ref-code"></strong></p>
                    <p>Payment Type: <strong class="transaction-type"></strong></p>
                    <p>Payment Status: <strong class="transaction-status"></strong></p>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <pre class="transaction-detail"></pre>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-lg btn-danger m-t-5 pull-right" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade slide-right show" id="show-details-modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-sm" style="max-width: 50vw; margin: 0 auto;">
        <div class="modal-content-wrapper">
            <div class="modal-content modal-item">
                <div class="modal-header clearfix text-left">
                    <h5>Payment Detail</h5>
                    <p>Ref# <strong class="payment-ref-code"></strong></p>
                    <p>Client: <strong class="payment-client"></strong></p>
                    <p>Email: <strong class="payment-email"></strong></p>
                    <p>Total Amount: <strong class="payment-total"></strong></p>
                    <p>Payment Options: <strong class="payment-options"></strong></p>
                    <p>Payment Type: <strong class="payment-type"></strong></p>
                    <p>Payment Status: <strong class="payment-status"></strong></p>
                    <p>Status: <strong class="payment-active"></strong></p>
                    <p>Expired At: <strong class="payment-expired"></strong></p>
                    <p>Created At: <strong class="payment-created"></strong></p>
                    <p>Remarks: <strong class="payment-remarks"></strong></p>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="payment-contents m-b-20"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-lg-9"></div>
                        <div class="col-md-6 col-lg-3">
                            <button type="button" class="btn btn-lg btn-danger btn-block m-t-5" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
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
<script>
$(document).on('click', '.btn-view', function (e) {
    e.preventDefault();
    $list_item = $(this).parents('.list-item');
    $modal_item = $('.modal-item');
    $modal_item.find('.payment-ref-code').html($list_item.find('.payment-ref-code').val());
    $modal_item.find('.payment-client').html($list_item.find('.payment-client').val());
    $modal_item.find('.payment-email').html($list_item.find('.payment-email').val());
    $modal_item.find('.payment-total').html($list_item.find('.payment-total').val());
    $modal_item.find('.payment-options').html($list_item.find('.payment-options').val());
    $modal_item.find('.payment-type').html($list_item.find('.payment-type').val());
    $modal_item.find('.payment-status').html($list_item.find('.payment-status').val());
    $modal_item.find('.payment-active').html($list_item.find('.payment-active').val());
    $modal_item.find('.payment-expired').html($list_item.find('.payment-expired').val());
    $modal_item.find('.payment-created').html($list_item.find('.payment-created').val());
    $modal_item.find('.payment-remarks').html($list_item.find('.payment-remarks').val());
    
    var contents = JSON.parse($list_item.find('.payment-contents').val()); console.log(contents);
    var content = '<table class="table table-hover table-responsive-block"><thead><tr><th>#</th><th>Title</th><th>Description</th><th>Link Title</th><th>Link URL</th><th>Amount</th></tr><tbody></thead>';
    if (contents != null) { 
        var ix=0;
        Object.entries(contents).forEach(([key, item]) => { ix++;
            content += '<tr><td>'+ix+'</td><td>'+item.title+'</td><td>'+(item.description != null ? item.description : "")+'</td><td>'+(item.link_title != null ? item.link_title : "")+'</td><td>'+(item.link_url != null ? item.link_url : "")+'</td><td>'+item.amount+'</td></tr>';
        });
    }
    else {
        content = '<tr><td colspan="6">No detail available.</td></tr>';
    }
    content += '</tbody></table>';
    $modal_item.find('.payment-contents').html(content);

    $('#show-details-modal').modal('show');
});
</script>
@endsection