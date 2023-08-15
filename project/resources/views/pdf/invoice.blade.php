<!DOCTYPE html>
<html>
<head>
	<title>{{ env('APP_NAME') }}</title>
</head>
<body style="font-family: -apple-system, Segoe UI, sans-serif; background: #fff;">
	<div style="margin: auto; padding: 0px;">
		<div style="margin-bottom: 0; font-size: 22px;">
			<div style="width: 50%; display: inline-block; padding-top:50px; padding-bottom: 0;">
				<img src="{{ $logo }}" style="width: 200px; height: 190px; margin: 0;">
			</div>
			<div style="width: 50%; display: inline-block; text-align: right;">
				<p>{{ env('APP_NAME') }}</p>
				<p>{{ env('CLINET_ADDRESS') }}</p>
				<p>{{ env('CLIENT_EMAIL') }}</span></p>
				<p>{{ env('CLIENT_PHONE') }}</span></p>
			</div>
		</div>
		<div style="margin-bottom: 30px; font-size: 25px;">
			<h3 style="text-transform: uppercase; margin-bottom: 10px; padding: 0; letter-spacing: 1.2px;">
				Receipt #{{ $data->ref_code }}
			</h3>
			<p>Date : {{ date('Y-m-d', strtotime($data->created_at)) }}</p>
			<p>To: {{ $data->client->name }}</p>
		</div>
		@php
		$contents = json_decode($data->contents);
		@endphp
		<div style="margin-bottom: 30px;">
			<table style="width: 100%; border: 1px solid #e9ecef; margin-bottom: 15px; background-color: transparent; font-size: 20px;">
				<thead>
					<tr>
						<th style="vertical-align: bottom; border: 1px solid #e9ecef; padding: 15px; text-align: center;">
							<p>Particular - {{ $data->title }}</p>
						</th>
						<th style="vertical-align: bottom; border: 1px solid #e9ecef; padding: 15px; text-align: center;">
							<p>Amount</p>
						</th>
					</tr>
				</thead>
				<tbody>
					@forelse($contents as $content)
					<tr>
						<td style="vertical-align: top; padding: 15px; text-align: left; border: 1px solid #e9ecef;">
							<p>{{ $content->title }}</p>
							<small>{{ $content->description }}</small>
						</td>
						<td style="vertical-align: top; padding: 15px; text-align: right; border: 1px solid #e9ecef;">
							<p>{{ $data->currency }} {{ $content->amount }}</p>
						</td>
					</tr>
					@empty
					<tr>
						<td style="vertical-align: top; padding: 15px; text-align: left; border: 1px solid #e9ecef;">
							<p>{{ $data->title }}</p>
						</td>
						<td style="vertical-align: top; padding: 15px; text-align: right; border: 1px solid #e9ecef;">
							<p>{{ $data->currency }} {{ number_format($data->total, 2) }}</p>
						</td>
					</tr>
					@endforelse
					<tr>
						<td colspan="2" style="vertical-align: top; padding: 15px; text-align: right; border: 1px solid #e9ecef;">
							<h4>Grand Total : {{ $data->currency }} {{ number_format($data->total, 2) }}</h4>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		
		<div style="text-align: center; margin-top: 50px; border-top: 1px solid #cccccc; padding-top: 20px;">
		    <small style="font-size: 18px;"><i style="color: #888;">This is a system generated invoice.</i></small>
		</div>
	</div>
</body>
</html>