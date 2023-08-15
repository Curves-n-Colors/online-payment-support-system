@extends('layouts.frontend')

@section('title', 'Payment Success')

@section('content')
<main class="page payment-page">
	<section class="payment-form d-flex align-items-center min-vh-100">
		<div class="container">
			<video playsinline="" autoplay="" muted="" loop="" width="100%" height="450">
				<source src="{{ asset('assets/video/success.mp4') }}" type="video/mp4">
			</video>
			<div class="block-heading pt-0">
				<h2>Your payment transaction was successful.</h2>
				<p>Ref# <strong>{{ $ref_code }}</strong></p>
			</div>
			<div class="block-heading pt-0" style="margin-top: 30px;">
				<h2><a href="{{ env('CLIENT_DOMAIN') }}" target="_blank">{{ env('APP_NAME') }}</a></h2>
			</div>
		</div>
	</section>
</main>
@endsection