@extends('layouts.frontend')

@section('title', 'Pay Now')

@section('content')
@php 
$khalti = false;
$payment_options = config('app.addons.payment_options');
$contents = ($entry->contents != '') ? json_decode($entry->contents) : '';
$payment_opts = ($entry->payment_options != '') ? json_decode($entry->payment_options, true) : '';
@endphp

<main class="page payment-page">
	<section class="payment-form dark d-flex align-items-center min-vh-100">
		<div class="container">
			<div class="block-heading">
				<h2>Make Your Payment</h2>
				<div>and continue using our services</div>
			</div>
			<form action="{{ route('pay.proceed', [$encrypt]) }}" method="POST" id="checkout-form">
				@csrf
				@method('PUT')
				<div class="products">
					<p class="title">{{ $entry->title }}</p>
					<h3 class="title">{{ $entry->client->name }}</h3>
					@forelse ($contents as $content)
					<div class="item">
						<span class="price">{{ $entry->currency }} {{ $content->amount }}</span>
						<div class="item-name">{{ $content->title }}</div>
						<div class="item-description">{{ $content->description }}</div>
						{{-- @if ($content->link_url != '')
						<a href="{{ $content->link_url }}" class="item-description">
							{{ $content->link_title ?? 'Click Here' }}
						</a>
						@endif --}}
					</div>
					@empty
					<div class="item">
						<div class="item-name">No details available</div>
					</div>
					@endforelse
					<div class="total">Sub-Total<span class="price">{{ $entry->currency }}<span>{{ number_format($entry->sub_total, 2) }}</span></span></div>
					<div class="total">VAT<span class="price">{{ $entry->currency }}<span>{{ number_format($entry->vat, 2) }}</span></span></div>
					<div class="advance_payment">
						<div class="total">Advance Payment <span class="price">x <span id="no_months">2</span> MONTHS</span></div>
					</div>
					
					<div class="total">Total<span class="price">{{ $entry->currency }}<span id="amount">{{ number_format($entry->total, 2) }}</span></span></div>
				</div>
				<div class="card-details">
					<h3 class="title">Payment Option</h3>
					@php $ix = 0; @endphp
					@forelse ($payment_options as $pay_opt)
						@php $ix++; @endphp
						@if ($pay_opt['code'] == 'KHALTI') @php $khalti = true; @endphp @endif
						@if (in_array($pay_opt['code'], $payment_opts))
						<div class="row">
							<div class="col-12">
								<input id="option-{{ $pay_opt['code'] }}" name="payment_type" type="radio" @if($ix==1) checked @endif value="{{ $pay_opt['code'] }}" />
	                            <label class="pointer" for="option-{{ $pay_opt['code'] }}" title="{{ $pay_opt['name'] }}">{{ $pay_opt['title'] }}</label>
							</div>
						</div>
						@endif
					@empty
					<div class="item">
						<div class="item-name">No payment option available</div>
					</div>
					@endforelse
					@error('payment_type')
                        <label class="error">{{ $message }}</label>
                    @enderror
					
					@if(isset($diff) and $diff>1)
					<div class="advance">
						<div class="item form-group" id="advance_month">
							<label for="selected_month"><strong>Select no. of months for the advance pay.</strong></label>
							<select class="form-control" id="selected_month" name="selected_month" autocomplete="off">
								@for($i=2; $i<=$diff; $i++)
								<option value={{ $i }}>{{ $i.' MONTHS' }}</option>
								@endfor
							</select>
						</div>
						
						<div class="item form-check">
							<input type="checkbox" class="form-check-input" id="is_advance" name="is_advance" value="1" autocomplete="off">
							<label class="form-check-label" for="is_advance">Do you want to advance pay?</label>
						</div>
					</div>
					@endif
					
					@if ($payment_options)
					<div class="row">
						<div class="form-group col-sm-12">
							<button type="submit" class="btn btn-primary btn-block" id="payment-button">Proceed to Payment</button>
							@if($khalti)
							<input type="hidden" name="khalti_token" id="khalti-token">
							<input type="hidden" name="khalti_account" id="khalti-account">
							@endif
						</div>
					</div>
					@endif
				</div>
				
			</form>
			<div class="block-heading">
				<h4><a href="{{ env('CLIENT_DOMAIN') }}" target="_blank">{{ env('APP_NAME') }}</a></h4>
			</div>
		</div>
	</section>
</main>

@endsection

@section('page-specific-script')
<script>
	var amount = {!!json_encode(($entry->total)) !!};
	
	var title = {!!json_encode($entry->title) !!};

	var subTitle = title.indexOf('(');

	var paymentTitle = title.substring(0, subTitle)
	console.log(paymentTitle);


	var startDate = {!!json_encode($entry->start_date) !!};

	
	$('#advance_month').hide();
	$('.advance_payment').hide();

	$('#is_advance').click(function(){
		if($(this).is(':checked')){
			$('#advance_month').show();
		}else{
			$('#advance_month').hide();
			$('.advance_payment').hide();
			$('#amount').text(amount);
			$('p.title').text(title);
		}
	});

	$('#selected_month').change(function(){
		var value = $(this).val();
		
		var advanceAmount = parseFloat(value * parseFloat(amount) ).toFixed(2);
		
		console.log(amount);
		if(value>0){
			$('.advance_payment').show();
			$("#no_months").text(value);
			$('#amount').text(advanceAmount);
			//ADD DATE IN STARTING DATE ONLY WHEN MONTH IS SELECTED
			const date = new Date(startDate);
			date.setMonth(date.getMonth() + parseInt(value));
			var endDate=date.toISOString().slice(0, 10);
			var newTitle = `${paymentTitle} ( ${startDate} TO ${endDate})`;
			$('p.title').text(newTitle);
			console.log(newTitle);

		}else{
			$('.advance_payment').hide();
		}
		console.log(advanceAmount);
	})
</script>
@if($khalti)
<script src="{{ $payment_options['KHALTI']['script'] }}"></script>
<script>
	var payment_total  = "{{ $entry->total }}";
	var checkout_form  = document.getElementById('checkout-form');
	var payment_button = document.getElementById('payment-button');

    var khalti_config = {
        "publicKey": "{{ $payment_options['KHALTI']['public_key'] }}",
        "productIdentity": "{{ $entry->uuid }}",
        "productName": "{{ $entry->client->name }}",
        "productUrl": "{{ env('CLIENT_DOMAIN') }}",
        "paymentPreference": [
            "MOBILE_BANKING",
            "KHALTI",
            "EBANKING",
            "CONNECT_IPS",
            "SCT",
        ],
        "eventHandler": {
            onSuccess (payload) {
                document.getElementById('khalti-token').value = payload.token;
                document.getElementById('khalti-account').value = payload.mobile; 
                checkout_form.submit();
            },
            onError (error) {
                console.log(error);
            },
            onClose () {
                console.log('widget is closing');
            }
        }
    };
    var khalti_checkout = new KhaltiCheckout(khalti_config);

    payment_button.onclick = function (e) {
    	e.preventDefault(); console.log(document.querySelector('input[name="payment_type"]:checked').value);
        if (document.querySelector('input[name="payment_type"]:checked').value == 'KHALTI') {
        	khalti_checkout.show({amount: payment_total * 100});
        }
        else { 
        	checkout_form.submit();
        }
    }


</script>
@endif

@endsection