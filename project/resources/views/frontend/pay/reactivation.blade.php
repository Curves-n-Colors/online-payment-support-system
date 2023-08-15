@extends('layouts.frontend')

@section('title', 'Servie Suspended')

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
            @if (session()->has('msg'))
                    <div class="alert alert-success">
                        <p>{{ session()->get('msg') }}</p>
                    </div>
                @endif
			<div class="block-heading">
                <h2> Service Suspended </h2>
				<div>You  can reactivate & continue using our services</div>
                
			</div>
			<form action="{{ route('payment.reactivate',[$encrypt])}}" method="POST" id="checkout-form">
				@csrf

				<div class="products">
					<p>{{ $entry->title }}</p>
					<h3 class="title">{{ $entry->client->name }}</h3>
					@forelse ($contents as $content)
					<div class="item">
						<span class="price">{{ $entry->currency }} {{ $content->amount }}</span>
						<div class="item-name">{{ $content->title }}</div>
						<div class="item-description">{{ $content->description }}</div>
						@if ($content->link_url != '')
						<a href="{{ $content->link_url }}" class="item-description">
							{{ $content->link_title ?? 'Click Here' }}
						</a>
						@endif
					</div>
					@empty
					<div class="item">
						<div class="item-name">No details available</div>
					</div>
					@endforelse
					<div class="total">Total<span class="price">{{ $entry->currency }} {{ number_format($entry->total, 2) }}</span></div>
				</div>
				<div class="card-details">

					<div class="row">
                        @if($entry['is_reactivate_request'] != 10)
						<div class="form-group col-sm-12">
							<button type="submit" class="btn btn-primary btn-block">  Service Reactivation Request </button>
						</div>
                        @endif
					</div>

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
