@extends('layouts.frontend')

@section('title', 'Service Suspended')

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
				<h2>Service Suspended</h2>
			</div>
			<form action=" " method="POST" id="checkout-form">
				@csrf
				@method('PUT')
				<div class="products">

					<h3 class="title">Hello {{ $entry->client->name }}</h3>
                    <p>Your Service for {{ $entry->title }} has been suspended. Click below to request for reactivation .</p>
				</div>
				<div class="card-details">


					@if ($payment_options)
					<div class="row">
						<div class="form-group col-sm-12">
							<button type="submit" class="btn btn-primary btn-block" id="payment-button"> Reactivation Request</button>
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
