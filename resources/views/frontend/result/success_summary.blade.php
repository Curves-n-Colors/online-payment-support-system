@extends('layouts.frontend')

@section('title', 'Payment Summary')

@section('content')
@php 
$payment_options = config('app.addons.payment_options');
$contents = ($detail->contents != '') ? json_decode($detail->contents) : '';
@endphp
<main class="page payment-page">
    <section class="payment-form d-flex align-items-center min-vh-100">
		<div class="container">
            <video playsinline="" autoplay="" muted="" loop="" width="100%" height="300">
                <source src="{{ asset('assets/video/success.mp4') }}" type="video/mp4">
            </video>
            <form>
                <div class="products">
                    <h4 style="text-align: center;">Your payment was successful</h4><br>
                    <p><strong>Payment Summary</strong></p>
                    <p>Ref# <strong>{{ $detail->ref_code }}</strong></p>
                    <h3 class="title">{{ $detail->client->name }}</h3> 
                    @forelse ($contents as $content)
                    <div class="item">
                        <span class="price">{{ $detail->currency }} {{ $content->amount }}</span>
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
                    <div class="total">Total<span class="price">{{ $detail->currency }} {{ number_format($detail->total, 2) }}</span></div>
                </div>
            </form>
            <div class="block-heading">
				<h4><a href="{{ env('CLIENT_DOMAIN') }}" target="_blank">{{ env('APP_NAME') }}</a></h4>
			</div>
		</div>
	</section>
</main>
@endsection