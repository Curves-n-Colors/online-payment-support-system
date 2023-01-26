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
                            <button type="button" id="payment-send-proceed-btn" data-random="" class="btn btn-lg btn-primary btn-block m-t-5 btn-proceed">Proceed</button>
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
@stop

@section('page-specific-style')
<link rel="stylesheet" href="{{ asset('assets/plugins/select2/select2.min.css') }}"/>
<link rel="stylesheet" href="{{ asset('assets/plugins/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
@endsection

@section('page-specific-script')
<script src="{{ asset('assets/plugins/select2/select2.full.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/plugins/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('assets/plugins/jquery-autonumeric/autoNumeric.js') }}"></script>
<script>
$('[data-init-plugin=select2]').select2();
$('.autonumeric').autoNumeric('init');
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
    var content = '<div class="form-group-attached payment-content m-b-10"> <div class="row"> <div class="col-md-8"> <div class="form-group form-group-default"> <div class="form-input-group"> <label>Title</label> <input type="text" class="form-control" name="contents['+idx+'][title]" placeholder="Title" required> </div></div></div><div class="col-md-4"> <div class="form-group form-group-default input-group"> <div class="form-input-group"> <label>Amount</label> <input type="text" data-a-dec="." data-a-sep="," class="autonumeric form-control calc-amt" placeholder="Amount" name="contents['+idx+'][amount]" required> </div><div class="input-group-append"> <span class="input-group-text"> <button type="button" class="btn btn-danger btn-remove">X</button> </span> </div></div></div></div><div class="form-group form-group-default"> <label>Description</label> <textarea name="contents['+idx+'][description]" class="form-control" placeholder="Description" style="height: 100px;"></textarea> </div><div class="row clearfix"> <div class="col-md-6"> <div class="form-group form-group-default"> <label>Link Title</label> <input type="text" class="form-control" name="contents['+idx+'][link_title]" placeholder="Link Title"> </div></div><div class="col-md-6"> <div class="form-group form-group-default"> <label>Link URL</label> <input type="url" class="form-control" name="contents['+idx+'][link_url]" placeholder="Link URL"> </div></div></div></div>';
    $('.payment-container').append(content);
    $('.autonumeric').autoNumeric('init');
});
$(document).on('click', '.btn-remove', function (e) { e.preventDefault(); $(this).parents('.payment-content').hide('slow', function () { $(this).remove(); calc_total(); }); });
$(document).on('blur', '.calc-amt', function (e) { calc_total(); });
$(document).on('change', '.select-client', function (e) { $('.email-client').val($(this).find('option:selected').data('email')); });
$(document).on('change', '.select-currency', function (e) { $('.payment-option').prop('checked', false).prop('disabled', true); $('.'+$(this).val()).each(function () { $(this).prop('checked', true).prop('disabled', false); }); });

var $modal = $("#master-auth-modal"),
    $form = $("#master-auth-form"),
    $pass = $("#master-auth-password");
$(document).on('click', '.create-and-send-payment', function(e) {
    e.preventDefault();
    $("#setup_option").val(1);

    if($('input[type="checkbox"]').prop("checked") == true){
        $form.attr("action", $(this).data("url"));
        $modal.modal("show");
        $pass.val("");
        $(".btn-proceed").html("Proceed").prop("disabled", false);
    } else {        
        notify_bar("danger", 'Payment Setup must be checked active before submitting.');
    }
    
})
  
$(document).on("click", "#payment-send-proceed-btn", function (e) {
    e.preventDefault();
    var $this = $(this);
    if ($pass.val() == "") {
        notify_bar("danger", "Enter Your Master Password");
        return false;
    }
    $.ajax({
        url: '/payment-setup/pin-verify',
        type: "POST",
        data: $form.serialize(),
        async: true,
        beforeSend: function () {
            $this.html("Loading...").prop("disabled", true);
        },
        success: function (response) {
            $pass.val("").html("Proceed").prop("disabled", false);
            if (response.status) {
                document.getElementById("payment-setup-form").submit();

            } else {
                notify_bar("danger", response.msg);
                $pass.val("");
                $this.html("Proceed").prop("disabled", false);
            }
        }
    });
});
</script>
@endsection