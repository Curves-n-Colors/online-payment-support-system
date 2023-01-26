@section('page-specific-modal')
<div class="modal fade stick-up show" id="master-auth-modal" data-backdrop="static" data-keyboard="false" style="padding: 0 !important;">
    <div class="modal-dialog modal-sm" style="max-width: 500px; margin: 0 auto;">
        <div class="modal-content">
            <div class="modal-header clearfix text-left">
                <h5>Enter Your Master PIN</h5>
                <p>Please verify your access before proceeding</p>
            </div>
            <div class="modal-body">
                <form action="" method="POST" id="master-auth-form">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group-attached m-b-15" id="list-entries" style="display:none;"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group form-group-default">
                                <label>Enter Your Master PIN</label>
                                <input type="password" class="form-control" required autofocus placeholder="Password" autocomplete="off" name="master_password" id="master-auth-password">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <button type="submit" id="payment-send-proceed-btn" data-random="" class="btn btn-lg btn-primary btn-block m-t-5 btn-proceed">Proceed</button>
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
                <h5>Enter Your Master PIN</h5>
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
                                    <span class="input-group-text">Ref# &nbsp;&nbsp;&nbsp;</span>
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
                                    <span class="input-group-text">Refund</span>
                                </div>
                                <div class="form-input-group">
                                    <label>Transaction Amount</label>
                                    <input type="text" class="form-control" readonly id="refund-total">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="display:none;">
                        <div class="col-md-12">
                            <div class="form-group form-group-default form-check-group">
                                <div class="form-check switch switch-lg danger full-width right m-b-15 m-t-15">
                                    <input type="checkbox" name="is_full" value="10" id="refund-full" checked>
                                <label for="refund-full">Full Refund ?</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="display:none;">
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
                                <label>Enter Your Master PIN</label>
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
<div class="modal fade slide-right show" id="show-details-modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-sm" style="max-width: 800px; margin: 0 auto;">
        <div class="modal-content-wrapper">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Payment Detail:</h5>
                            <pre class="m-t-0" id="payment-detail"></pre>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Payment Transaction:</h5>
                            <pre class="m-t-0" id="payment-transaction"></pre>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Payment Items:</h5>
                            <pre class="m-t-0" id="payment-contents"></pre>
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
@stop

@section('page-specific-style')
<link rel="stylesheet" href="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/plugins/select2/select2.min.css') }}"/>
<link rel="stylesheet" href="{{ asset('assets/plugins/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
<link href="{{ asset('assets/plugins/table/css/dataTables.bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/plugins/table/css/buttons.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/plugins/table/css/dataTables.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('page-specific-script')
<script src="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/plugins/select2/select2.full.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/plugins/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('assets/plugins/table/js/jquery.dataTables.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/plugins/table/js/dataTables.buttons.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/plugins/table/js/jszip.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/plugins/table/js/pdfmake.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/plugins/table/js/vfs_fonts.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/plugins/table/js/buttons.html5.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/js/table.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/js/master.auth.min.js') }}" type="text/javascript"></script>
<script>
$('[data-init-plugin=select2]').select2();
$('.datepicker').datepicker({
    keyboardNavigation : false,
    forceParse : false,
    calendarWeeks : false,
    autoclose : true,
    format: 'yyyy-mm-dd',
    todayHighlight: true
});
$(document).on('click', '.btn-view-more', function (e) {
    e.preventDefault();
    $('#payment-transaction').parents('.row').hide();
    $list_item = $(this).parents('.list-item');

    var detail = {};
    $list_item.find(".payment-item").each( function() {
        detail[$(this).data('title')] = $(this).val();
    });
    
    var contents = JSON.parse($list_item.find('.contents').val()); 
    document.querySelector('#payment-detail').innerHTML = JSON.stringify(detail, null, 3);
    document.querySelector('#payment-contents').innerHTML = JSON.stringify(contents, null, 3);

    var transactions = $list_item.find('.transactions').val();
    
    if (typeof transactions != 'undefined' && transactions != '[]') {
        $('#payment-transaction').parents('.row').show();
        document.querySelector('#payment-transaction').innerHTML = JSON.stringify(JSON.parse(transactions), null, 3);
    }
    $('#show-details-modal').modal('show');
});
$(document).on('click', '.btn-get-entires', function (e) {
    e.preventDefault();
    var $this   = $(this);
    var random  = $this.data('random');

    $.ajax({
        url: $this.data('action'),
        type: 'PUT',
        async: false,
        success: function (response) {
            var content = '';
            if (response.status) {
                if (response.entries) {
                    Object.entries(response.entries).forEach(([key, value]) => {
                        content += '<div class="form-group form-group-default form-check-group d-flex align-items-center p-t-10 p-b-10">'+
                                '<div class="form-check switch danger full-width left m-b-0">'+
                                    '<input type="checkbox" name="entries[]" id="entries-'+key+'" value="'+value.uuid+'" aria-invalid="false">'+
                                    '<label class="text-right" for="entries-'+key+'">RESEND - '+value.title+'</label>'+
                                '</div>'+
                            '</div>';
                    });
                }
                if (response.new_entry) {
                    content += '<div class="form-group form-group-default form-check-group d-flex align-items-center p-t-10 p-b-10">'+
                                    '<div class="form-check switch complete full-width left m-b-0">'+
                                        '<input type="checkbox" name="entries[]" value="new" id="entries-new" aria-invalid="false" checked>'+
                                        '<label class="text-right" for="entries-new">SEND - NEW PAYMENT ('+(response.new_entry.old_payment_date ? response.new_entry.old_payment_date + ' to ' : '') + response.new_entry.new_payment_date+')</label>'+
                                    '</div>'+
                                '</div>';
                }
            }
            $('#list-entries').html(content);
            $('#payment-send-proceed-btn').attr('data-random', random);
        }
    });
});
</script>
@endsection