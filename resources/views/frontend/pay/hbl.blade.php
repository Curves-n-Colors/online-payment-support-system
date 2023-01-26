<form class="hbl-payment-request" method="post" action="https://hblpgw.2c2p.com/HBLPGW/Payment/Payment/Payment"> 
    {{-- <form class="hbl-payment-request" method="post" action="https://hbl.pgw/payment"> --}}
    @csrf
        <input type="hidden" name="paymentGatewayID" value="{{ $hbl['gateway_id'] }}">
        <input type="hidden" name="invoiceNo" value="{{ $hbl['invoiceNo'] }}">
        <input type="hidden" name="productDesc" value="{{ $hbl['productDesc'] }}">
        <input type="hidden" name="Amount" value="{{ $hbl['amount'] }}">
        <input type="hidden" name="currencyCode" value="{{ $hbl['currencyCode'] }}">
        <input type="hidden" name="userDefined1" value="{{ $hbl['userDefined1'] }}">
        <input type="hidden" name="userDefined2" value="{{ $hbl['userDefined2'] }}">
        <input type="hidden" name="userDefined4" value="{{ $hbl['userDefined4'] }}">
        <input type="hidden" name="nonSecure" value="{{ $hbl['nonSecure'] }}"> 
        <input type="hidden" name="hashValue" value="{{ $hbl['hashValue'] }}">
    </form>
    
    <script type="text/javascript">
        document.querySelector(".hbl-payment-request").submit();
    </script>