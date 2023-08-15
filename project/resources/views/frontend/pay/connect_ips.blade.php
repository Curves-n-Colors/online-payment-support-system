<form method="POST" action="{{$url}}" id="connectips-payment-request">
    <div class="row">
        <div class="col-sm-12 mb-4">
            <div class="js-form-message">
                <label class="form-label">
                    Merchant ID
                </label>
                <input type="text" class="form-control" name="MERCHANTID" id="MERCHANTID" value="{{$data['MERCHANTID']}}" />
            </div>
        </div>
        <div class="col-sm-12 mb-4">
            <div class="js-form-message">
                <label class="form-label">
                    App ID
                </label>

                <input type="text" class="form-control" name="APPID" id="APPID" value="{{$data['APPID']}}" />
            </div>
        </div>
        <div class="col-sm-12 mb-4">
            <div class="js-form-message">
                <label class="form-label">
                    App Name
                </label>
                <input type="text" class="form-control" name="APPNAME" id="APPNAME" value="{{$data['APPNAME']}}" />
            </div>
        </div>
        <div class="col-sm-12 mb-4">
            <div class="js-form-message">
                <label class="form-label">
                    Txn ID
                </label>
                <input type="text" class="form-control" name="TXNID" id="TXNID" value="{{$data['TXNID']}}" />
            </div>
        </div>
        <div class="col-sm-12 mb-4">
            <div class="js-form-message">
                <label class="form-label">
                    TXN DATE
                </label>
                <input type="text" class="form-control" name="TXNDATE" id="TXNDATE" value="{{$data['TXNDATE']}}" />
            </div>
        </div>
        <div class="col-sm-12 mb-4">
            <div class="js-form-message">
                <label class="form-label">
                    TXN CRNCY
                </label>
                <input type="text" class="form-control" name="TXNCRNCY" id="TXNCRNCY" value="{{ $data['TXNCRNCY'] }}" />
            </div>
        </div>
        <div class="col-sm-12 mb-4">
            <div class="js-form-message">
                <label class="form-label">
                    TXN AMT
                </label>
                <input type="text" class="form-control" name="TXNAMT" id="TXNAMT" value="{{ $data['TXNAMT'] }}" />
            </div>
        </div>
        <div class="col-sm-12 mb-4">
            <div class="js-form-message">
                <label class="form-label">
                    REFERENCE ID
                </label>
                <input type="text" class="form-control" name="REFERENCEID" id="REFERENCEID" value="{{ $data['REFERENCEID'] }}" />
            </div>
        </div>
        <div class="col-sm-12 mb-4">
            <div class="js-form-message">
                <label class="form-label">
                    REMARKS
                </label>
                <input type="text" class="form-control" name="REMARKS" id="REMARKS" value="Remarks for user {{ $data['REMARKS'] }}" />
            </div>
        </div>
        <div class="col-sm-12 mb-4">
            <div class="js-form-message">
                <label class="form-label">
                    PARTICULARS
                </label>
                <input type="text" class="form-control" name="PARTICULARS" id="PARTICULARS" value="particulars for user {{ $data['PARTICULARS'] }}" />
            </div>
        </div>
        <div class="col-sm-12 mb-4">
            <div class="js-form-message">
                <label class="form-label">
                    TOKEN
                </label>
                <input type="text" class="form-control" name="TOKEN" id="TOKEN" value="{{ $data['TOKEN'] }}" />
            </div>
        </div>

    </div>
</form>


<script>
    document.getElementById('connectips-payment-request').submit();
</script>