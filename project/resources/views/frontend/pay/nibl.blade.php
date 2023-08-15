<form action="{{ $nibl['iPay_URL'] }}" id="nibl-payment-request" method="POST"> 
	<input type="hidden" name="TXN_UUID" value="{{ $nibl['txn_uuid'] }}">
</form>

<script type="text/javascript">
	document.getElementById('nibl-payment-request').submit();
</script>