<form action="https://uat.esewa.com.np/epay/main" id="esewa-payment-request" method="POST">
    <input value="{{ $entry->total }}" name="tAmt" type="hidden">
    <input value="{{ $entry->total }}" name="amt" type="hidden">
    <input value="0" name="txAmt" type="hidden">
    <input value="0" name="psc" type="hidden">
    <input value="0" name="pdc" type="hidden">
    <input value="epay_payment" name="scd" type="hidden">
    <input value="{{ uniqid() }}" name="pid" type="hidden">
    <input value="{{ route('esewa.success') }}" type="hidden" name="su">
    <input value="{{ route('result.failed') }}" type="hidden" name="fu">
</form>
<script type="text/javascript">
    document.getElementById('esewa-payment-request').submit();
</script>