<!DOCTYPE html>
<html>
<head>
    <title>@yield('title')</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Varela+Round&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/payment.min.css') }}">
</head>
<body>
    <main class="page payment-page">
        <section class="payment-form d-flex align-items-center min-vh-100">
            <div class="container">
                <video playsinline="" autoplay="" muted="" loop="" width="100%" height="350">
                    <source src="{{ asset('assets/video/watermelon.mp4') }}" type="video/mp4">
                </video>
                <div class="block-heading">
                    <h1><strong>@yield('code')</strong></h1>
                    <h3>@yield('message')</h3>
                    <div style="margin-top: 30px;"><a href="https://curvesncolors.com">Curves n' Colors</a></div>
                </div>
            </div>
        </section>
    </main>
</body>
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
</body>
</html>