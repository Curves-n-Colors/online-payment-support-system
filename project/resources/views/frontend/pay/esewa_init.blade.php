<form action="{{ $esewa['request_url'] }}" id="esewa-payment-request" method="POST">
    <input value="{{ $amount }}" name="tAmt" type="hidden">
    <input value="{{ $amount }}" name="amt" type="hidden">
    <input value="0" name="txAmt" type="hidden">
    <input value="0" name="psc" type="hidden">
    <input value="0" name="pdc" type="hidden">
    <input value="{{ $esewa['merchant_id'] }}" name="scd" type="hidden">
    <input value="{{ $entry->min_uuid }}" name="pid" type="hidden">
    <input value="{{ route('esewa.success') }}" type="hidden" name="su">
    <input value="{{ route('result.failed') }}" type="hidden" name="fu">
</form>
<script type="text/javascript">
    document.getElementById('esewa-payment-request').submit();
</script>