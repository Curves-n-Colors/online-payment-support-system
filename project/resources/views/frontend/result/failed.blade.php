@extends('layouts.frontend')

@section('title', 'Payment Failed')

@section('content')
<main class="page payment-page">
    <section class="payment-form d-flex align-items-center min-vh-100">
        <div class="container">
            <video playsinline="" autoplay="" muted="" loop="" width="100%" height="350">
                <source src="{{ asset('assets/video/watermelon.mp4') }}" type="video/mp4">
            </video>
            <div class="block-heading">
                <h2>Your payment transaction has failed.</h2>
                <p>Please contact <a href="mailto:{{ env('PRIMARY_MAIL') }}" target="_blank">our team</a> for further detail.</p>
            </div>
            <div class="block-heading pt-0" style="margin-top: 30px;">
                <h2><a href="{{ env('CLIENT_DOMAIN') }}" target="_blank">{{ env('APP_NAME') }}</a></h2>
            </div>
        </div>
    </section>
</main>
@endsection