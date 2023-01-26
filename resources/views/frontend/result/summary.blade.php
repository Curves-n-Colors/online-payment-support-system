@extends('layouts.frontend')

@section('title', 'Payment Summary')

@section('content')
@php 
$contents = ($detail->contents != '') ? json_decode($detail->contents) : '';
@endphp
<main class="page payment-page">
	<section class="payment-form dark d-flex align-items-center min-vh-100">
		<div class="container">
			<div class="block-heading">
				<h2>Your Payment Summary</h2>
				<div>{{ env('CLIENT_DOMAIN') }}</div>
			</div>
            <div class="products">
                <p>Ref# {{ config('app.addons.ref_code_prefix') }}-{{ $detail->ref_code }}</p>
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
            <div class="card-details">
                <h3 class="title">Payment Option</h3>
                @forelse ($payment_options as $pay_opt)
                    @if ($pay_opt['code'] == $detail->payment_type)
                    <div class="row">
                        <div class="col-12">
                            <input id="option-{{ $pay_opt['code'] }}" type="radio" checked />
                            <label class="pointer" for="option-{{ $pay_opt['code'] }}" title="{{ $pay_opt['name'] }}">{{ $pay_opt['title'] }}</label>
                        </div>
                    </div>
                    @endif
                @empty
                <div class="item">
                    <div class="item-name">No payment option available</div>
                </div>
                @endforelse
            </div>
			<div class="block-heading">
				<h4><a href="{{ env('CLIENT_DOMAIN') }}" target="_blank">{{ env('APP_NAME') }}</a></h4>
			</div>
		</div>
	</section>
</main>
@endsection