<form action="{{ $fonepay['request_url'] }}" id="fonepay-payment-request" method="GET">
    <input type="hidden" name="PID" value="{{ $fonepay['PID'] }}">
    <input type="hidden" name="MD" value="{{ $fonepay['MD'] }}">
    <input type="hidden" name="AMT" value="{{ $fonepay['AMT'] }}">
    <input type="hidden" name="CRN" value="{{ $fonepay['CRN'] }}">
    <input type="hidden" name="DT" value="{{ $fonepay['DT'] }}">
    <input type="hidden" name="R1" value="{{ $fonepay['R1'] }}">
    <input type="hidden" name="R2" value="{{ $fonepay['R2'] }}">
    <input type="hidden" name="DV" value="{{ $fonepay['DV'] }}">
    <input type="hidden" name="RU" value="{{ $fonepay['RU'] }}">
    <input type="hidden" name="PRN" value="{{ $fonepay['PRN'] }}">
</form>

<script type="text/javascript">
    document.getElementById('fonepay-payment-request').submit();
</script>