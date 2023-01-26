<!DOCTYPE html>
<html>
<head>
	<title>@yield('title') | {{ env('APP_NAME') }}</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="{{ asset('assets/img/favicon.ico') }}" rel="icon" type="image/x-icon" />
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
	<link href="https://fonts.googleapis.com/css2?family=Varela+Round&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="{{ asset('assets/css/payment.min.css') }}">
</head>
<body>
	@yield('content')
</body>
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
@yield('page-specific-script')
</body>
</html>