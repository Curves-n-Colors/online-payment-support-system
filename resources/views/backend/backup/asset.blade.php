@section('page-specific-style')
<link rel="stylesheet" href="{{ asset('assets/plugins/select2/select2.min.css') }}"/>
<link rel="stylesheet" href="{{ asset('assets/plugins/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/plugins/bootstrap-timepicker/bootstrap-timepicker.min.css') }}">
@endsection

@section('page-specific-script')
<script src="{{ asset('assets/plugins/select2/select2.full.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/plugins/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('assets/plugins/bootstrap-timepicker/bootstrap-timepicker.min.js') }}"></script>
<script src="{{ asset('assets/plugins/jquery-autonumeric/autoNumeric.js') }}"></script>
<script>
$('[data-init-plugin=select2]').select2();
$('.autonumeric').autoNumeric('init');
$('.timepicker').timepicker().val('');
$('.datepicker').datepicker({
    keyboardNavigation : false,
    forceParse : false,
    calendarWeeks : false,
    autoclose : true,
    startDate: '1d',
    format: 'yyyy-mm-dd',
    todayHighlight: true
});

var calc_total = function () {
    var total = 0;
    $('.calc-amt').each( function () {
        var amt = parseFloat($(this).val().split(',').join('')); 
        if (!isNaN(amt)) total += amt;
    });
    $('.total-amt').val(total);
}

var idx = $('.payment-content').length;
$(document).on('click', '.btn-add', function (e) {
    e.preventDefault(); idx++;
    var content = '<div class="form-group-attached payment-content m-b-10"> <div class="row"> <div class="col-md-8"> <div class="form-group form-group-default"> <div class="form-input-group"> <label>Title</label> <input type="text" class="form-control" name="contents['+idx+'][title]" placeholder="Title" required> </div></div></div><div class="col-md-4"> <div class="form-group form-group-default input-group"> <div class="form-input-group"> <label>Amount</label> <input type="text" data-a-dec="." data-a-sep="," class="autonumeric form-control calc-amt" placeholder="Amount" name="contents['+idx+'][amount]" required> </div><div class="input-group-append"> <span class="input-group-text"> <button type="button" class="btn btn-danger btn-remove">X</button> </span> </div></div></div></div><div class="form-group form-group-default"> <label>Description</label> <textarea name="contents['+idx+'][description]" class="form-control" placeholder="Description" style="height: 110px;"></textarea> </div><div class="row clearfix"> <div class="col-md-6"> <div class="form-group form-group-default"> <label>Link Title</label> <input type="text" class="form-control" name="contents['+idx+'][link_title]" placeholder="Link Title"> </div></div><div class="col-md-6"> <div class="form-group form-group-default"> <label>Link URL</label> <input type="url" class="form-control" name="contents['+idx+'][link_url]" placeholder="Link URL"> </div></div></div></div>';
    $('.payment-container').append(content);
    $('.autonumeric').autoNumeric('init');
});
$(document).on('click', '.btn-remove', function (e) { e.preventDefault(); $(this).parents('.payment-content').hide('slow', function () { $(this).remove(); calc_total(); }); });
$(document).on('blur', '.calc-amt', function (e) { calc_total(); });
$(document).on('change', '.select-client', function (e) { $('.email-client').val($(this).find('option:selected').data('email')); });
$(document).on('change', '.select-currency', function (e) { $('.payment-option').prop('checked', false).prop('disabled', true); $('.'+$(this).val()).each(function () { $(this).prop('checked', true).prop('disabled', false); }); });
$(document).on('change', '.select-recurrence', function (e){var val=$(this).val(); var $expiry=$('.expiry-details'); $expiry.find('input').val(''); (val==0) ? $expiry.show() : $expiry.hide();});

$(document).on('click', '.btn-show-transaction', function (e){e.preventDefault();var $this=$(this);var $tran_modal=$('#show-transaction-modal');$.ajax({url: $this.data('url'),type: 'PUT',async: false,success: function (response){if (response.status){$tran_modal.find('.transaction-ref-code').html($this.data('ref'));$tran_modal.find('.transaction-type').html($this.data('type'));$tran_modal.find('.transaction-status').html($this.data('status'));$tran_modal.find('.transaction-detail').html(JSON.stringify(response.transaction, undefined, 2));$tran_modal.modal('show');}else{notify_bar('danger', response.msg);}}});});

</script>
@endsection