<?php

namespace App\Http\Controllers\Frontend;

use DB;
use Exception;
use Carbon\Carbon;
use GuzzleHttp\Client;
use App\Models\Logs;
use App\Models\PayNibl;
use App\Models\PayKhalti;
use App\Models\PaymentHBL;
use App\Models\PaymentNibl;
use Illuminate\Support\Str;
use App\Models\PaymentEntry;
use App\Models\PaymentSetup;

use Illuminate\Http\Request;
use App\Models\PaymentDetail;

use App\Http\Requests\PayRequest;
use App\Http\Requests\NiblRequest;
use App\Models\HblPaymentResponse;
use App\Helpers\HBLPayment\Payment;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Services\Backend\PaymentEntryService;
use App\Services\Backend\PaymentEsewaService;
use App\Services\Backend\PaymentDetailService;
use App\Services\Backend\PaymentKhaltiService;
use App\Services\Backend\PaymentFonepayService;
use App\Services\Backend\PaymentConnectIpsService;
use App\Services\Backend\TempAdvanceDetailsService;


class PayController extends Controller
{
    public function index(Request $request, $encrypt)
    {

        $check = PaymentSetup::_validating($encrypt);
        if ($check['status'] == 200) {
            $entry = $check['entry'];
           
            $detail = $check['detail'];

            //FOR NO OF MONTHS FOR ADVANCE PAY
            $diff = 0;
            
            if(isset($entry->start_date) and isset($entry->subscription->expire_date)){
                $start_date = strtotime($entry->start_date);
                $end_date = strtotime($entry->subscription->expire_date);
                $year1 = date('Y', $start_date);
                $year2 = date('Y', $end_date);
                
                $month1 = date('m', $start_date);
                $month2 = date('m', $end_date);
                
                $diff = (($year2 - $year1) * 12) + ($month2 - $month1);
            }
            // dd($entry->start_date,  $entry->setup->expire_date, $diff);
            if ($entry) {
                return view('frontend.pay.index', compact('encrypt', 'entry', 'diff'));
            } else if ($detail) {
                return redirect()->route('result.success', [$encrypt]);
            }
        }
        return redirect()->route('result.error', [$check['status']]);
    }

    public function proceed(PayRequest $request, $encrypt)
    { 
        $check = PaymentSetup::_validating($encrypt);
        if ($check['status'] == 200) {
            $entry = $check['entry'];
            if ($entry) {
                if ($request->payment_type == 'NIBL') {
                    return $this->nibl_pay($entry, $request);
                } else if ($request->payment_type == 'KHALTI') {
                    return $this->khalti_pay($entry, $encrypt, $request);
                } else if ($request->payment_type == 'HBL') {
                    return $this->hbl_pay($entry, $encrypt, $request);
                } else if ($request->payment_type == 'ESEWA') {
                    return $this->esewa($entry, $encrypt, $request);
                } else if ($request->payment_type == 'FONEPAY') {
                    return $this->fonepay($entry, $encrypt, $request);
                } else if ($request->payment_type == 'CONNECT_IPS'){
                    return $this->connect_ips($entry, $encrypt, $request);
                }
            }
        }
        return redirect()->route('result.error', [$check['status']]);
    }

    public function nibl_pay($entry, $request)
    {
        $pay = new PayNibl();
        $nibl = $pay->process($entry);
        if ($nibl['status'] == 200) {
            return view('frontend.pay.nibl', compact('nibl'));
        }
        return redirect()->route('result.error', [$nibl['status']]);
    }

    // save failed transcation logs and status changes
    public function nibl_confirm(NiblRequest $request)
    {
        if (
            $request->txn_status == 'ACCEPTED'
            && $request->mer_var_2 == config('app.addons.private_key')
            && $entry = PaymentEntry::where('uuid', $request->mer_var_1)->where('is_active', 10)->first()
        ) {

            $pay = new PayNibl();
            $nibl = $pay->verify($entry, $request->all());

            if ($nibl['status'] == 200) {
                $model = PaymentNibl::_storing($entry->uuid, $nibl);

                if ($nibl['txn_status'] == 'ACCEPTED' && $model) {

                    $detail = [
                        'type'   => 'NIBL',
                        'status' => $model->status
                    ];

                    PaymentDetail::_storing($entry, $detail);
                    PaymentEntry::_deleting($entry->uuid);

                    return redirect()->route('result.success', [PaymentSetup::_encrypting($entry->setup->uuid, $entry->uuid)]);
                }
                return redirect()->route('result.failed');
            }
            return redirect()->route('result.error', [$nibl['status']]);
        }

        Logs::_set('NIBL Payment Error for payment - trans-ref-invalid, Action: SaleTxn, Error' . json_encode($request->all()), 'error-nibl');
        return redirect()->route('result.error', ['trans-ref-invalid']);
    }

    public function khalti_pay($entry, $encrypt, $request)
    {
        $transaction = [
            'account'   => $request->khalti_account,
            'pre_token' => $request->khalti_token,
            'amount'    => (int) (($entry->total > 200 ? 50 : 100)* 100),
        ];

        PaymentKhaltiService::_create($entry->uuid, $transaction);

        $khalti = PaymentKhaltiService::_verify($transaction);

        if ($khalti['status'] == 200) {

            $model = PaymentKhaltiService::_update($entry->uuid, $khalti['response']['idx']);
            $entry = PaymentEntryService::_find($entry->uuid);

            $detail = [
                'type'   => 'KHALTI',
                'status' => $model->status
            ];
            PaymentDetailService::_storing($entry, $detail);
            PaymentEntryService::_update_new_entry($entry->uuid);

            return redirect()->route('result.success', [$encrypt]);
        }

        PaymentKhaltiService::_update($entry, null);
        return redirect()->route('result.failed');
    }

    public function fonepay($entry, $encrypt, $request)
    {
        $amount = $entry['total'];

        if (isset($request->is_advance)) {
            //GENERATE NEW END DATE
            $advance = $this->advance_payment($entry, $request);
            $amount = $advance['amount'];
            $title = $advance['title'];

            TempAdvanceDetailsService::_store_transaction($entry['uuid'], $request->selected_month, $title, 'FONEPAY');
        }
        $fonepay_config = config('app.addons.payment_options.fonepay');
        $fonepay = [
            'MD' =>  $fonepay_config['MD'],
            'AMT' => $amount,
            'CRN' => $entry['currency'],
            'DT' => date('m/d/Y'),
            'R1' => 'test 123',
            'R2' => 'test 1222',
            'PRN' => $entry['min_uuid'],
            'PID' => $fonepay_config['PID'],
            'RU'     => route('fonepay.verify'),
            'request_url' => $fonepay_config['request_url'],
        ];
        
        $fonepay['DV'] = hash_hmac('sha512', $fonepay['PID'] . ',' . $fonepay['MD'] . ',' . $fonepay['PRN'] . ',' . $fonepay['AMT'] . ',' . $fonepay['CRN'] . ',' . $fonepay['DT'] . ',' . $fonepay['R1'] . ',' . $fonepay['R2'] . ',' . $fonepay['RU'], $fonepay_config['sharedSecretKey']);

        return view('frontend.pay.fonepay_init', compact('fonepay'));
    }

    public function fonepay_verify(Request $request)
    {
        try {
            $result      = PaymentFonepayService::_check($request);
            $save_result = PaymentFonepayService::_create($result, $request);

            if ($result['status'] == 200 && $save_result->status==10) {
                $model = PaymentEntryService::_find_min_uuid($save_result->prn);
                $detail = [
                    'type'   => 'FONEPAY',
                    'status' => $save_result->status
                ];

                $has_advance = TempAdvanceDetailsService:: _find_advance($model->uuid, $detail['type']);

                if ($has_advance) {
                    $model->title       = $has_advance->title;
                    $model->start_date  = $has_advance->start_date;
                    $model->end_date    = $has_advance->end_date;
                    $model->total       = $has_advance->amount;
                    $model->update();
                    $detail['advance_month'] = $has_advance->months;
                }

                PaymentDetailService::_storing($model, $detail);
                PaymentEntryService::_update_new_entry($model->uuid);

                return redirect()->route('result.success', [PaymentSetup::_encrypting($model->setup->uuid, $model->uuid)]);

            } else {
                return redirect()->route('result.failed');
            }
        } catch (Exception $e) {
            dd($e);
            dd(['status' => 'failed', 'msg' => 'Something went Wrong, Try Again']);
        }
    }

    public function hbl_pay($entry, $encrypt, $request)
    {
        $invoiceNo = str_pad(Carbon::now()->timestamp, 20, 0, STR_PAD_LEFT);
        $amount = $entry['total'];
        $title = $entry['title'];
        if(isset($request->is_advance)){
            //GENERATE NEW END DATE
            $advance = $this->advance_payment($entry, $request);
            $amount = $advance['amount'];
            $title = $advance['title'];
        }

        $pay = new Payment(
            $orderNo = $invoiceNo,
            $amount =(int) $amount,
            $productDescription =  $title,
            $amountText = str_pad($amount * 100, 12, 0, STR_PAD_LEFT),
            $currencyCode = $entry['currency'],
            $officeId = config('app.addons.payment_options.hbl.merchant_id'),
            $encryptCode = $encrypt, 
        );
        $response = $pay->ExecuteJose();

        if(isset($request->is_advance)){
            TempAdvanceDetailsService::_storing($entry['uuid'], $request->selected_month, $response);
        }

        if($response){
            HblPaymentResponse::_save_response($response,$entry['uuid']);
        }

        $response = json_decode($response, true);
        
        return  $response;
        return redirect($response['data']['paymentPage']['paymentPageURL']);

    }

    public function advance_payment($entry, $request){
        $end_date = date('Y-m-d', strtotime($entry->start_date . ' + ' . $request->selected_month . ' month'));

        $amount = $entry['total'] * $request->selected_month;

        $title = $entry->title;
        $index = strpos($title, "(");
        $type = substr($title, 0, $index);
        $title = $type . '(' . $entry->start_date . ' TO ' . $end_date . ')';
        return [
            'title' => $title,
            'amount' => $amount,
        ];
    }

    public function esewa($entry, $encrypt, $request)
    {
        $amount = $entry['total'];

        if (isset($request->is_advance)) {
            //GENERATE NEW END DATE
            $advance = $this->advance_payment($entry, $request);
            $amount = $advance['amount'];
            $title = $advance['title'];

            TempAdvanceDetailsService::_store_transaction($entry['uuid'], $request->selected_month, $title, 'ESEWA');
        }
        
        $esewa = config('app.addons.payment_options.esewa');
        return view('frontend.pay.esewa_init', compact('esewa', 'entry', 'encrypt', 'amount'));
    }

    public function esewa_success(Request $request){
        try {
            $result  = PaymentEsewaService::_check($request);
            if ($result['status']) {
                $save_result = PaymentEsewaService::_create($request);
                $model = PaymentEntryService::_find_min_uuid($request->oid);
                $detail = [
                    'type'   => 'ESEWA',
                    'status' => $save_result->status
                ];

                $has_advance = TempAdvanceDetailsService::_find_advance($model->uuid, $detail['type']);

                if ($has_advance) {
                    $model->title       = $has_advance->title;
                    $model->start_date  = $has_advance->start_date;
                    $model->end_date    = $has_advance->end_date;
                    $model->total       = $has_advance->amount;
                    $model->update();
                    $detail['advance_month'] = $has_advance->months;
                }

                PaymentDetailService::_storing($model, $detail);
                PaymentEntryService::_update_new_entry($model->uuid);

                return redirect()->route('result.success', [PaymentSetup::_encrypting($model->setup->uuid, $model->uuid)]);
            } 
        
        } catch (Exception $e) {
            dd($e);
        }
        return redirect()->route('result.failed');
    }

    public function proceedHbl($encrypt)//OLD HBL FUNCTION
    {
        $check = PaymentSetup::_validating($encrypt);
        if ($check['status'] == 200) {
            $entry = $check['entry'];
            $hbl = [];
            $encryptArray = str_split($encrypt, 150);

            $amount = str_pad($entry['total'] * 100, 12, 0, STR_PAD_LEFT);

        
            $invoiceNo = str_pad(Carbon::now()->timestamp, 20, 0, STR_PAD_LEFT);;

             // hash value calculation according to hbl payment API docs
            $signature_string = config('app.addons.payment_options.hbl.merchant_id') . $invoiceNo . $amount . "N";
            $signData = hash_hmac('SHA256', $signature_string, env('SECRET_CODE'), false);
            $strdata = strtoupper($signData);
            $hashValue = urlencode($strdata);

            $hbl['gateway_id']      = config('app.addons.payment_options.hbl.merchant_id');
            $hbl['secret_code']     = config('app.addons.payment_options.hbl.secret_key');
            $hbl['nonSecure']       = config('app.addons.payment_options.hbl.non_secure');
            $hbl['productDesc']     = $entry['title'];
            $hbl['hashValue']       = $hashValue;
            $hbl['currencyCode']    = config('app.addons.payment_options.hbl.currency_code.' . $entry['currency']);
            $hbl['invoiceNo']       = $invoiceNo;
            $hbl['amount']          = $amount;
            $hbl['userDefined1']    = $encryptArray[1];
            $hbl['userDefined2']    = $encryptArray[2];
            $hbl['userDefined4']    = $encryptArray[0];
            return view('frontend.pay.hbl', compact('hbl'));
        }
        
    }

    

    public function hblFrontendResponse(Request $request, $url_encryption)//OLD HBL FUNCTION
    {   
        try {
            $validator = Validator::make($request->all(), [
                'Amount'        => 'required',
                'respCode'      => 'required',
                'fraudCode'     => 'required',
                'approvalCode'  => 'required',
                'Eci'           => 'required',
                'Status'        => 'required',
                'userDefined1'  => 'required'
            ]);

            if ($validator->fails()) {
                return redirect()->route('result.failed')->with('error', 'Unauthorised request / payment has been cancelled by client.');
            }
   
            if ($url_encryption == config('app.addons.payment_options.hbl.url_encryption_key')) { 
                $encrypt = $request->userDefined4 . $request->userDefined1 . $request->userDefined2;
                $check = PaymentSetup::_validating($encrypt);
                if ($check['status'] == 200) {
                    $result = PaymentHBL::_create($check['entry'], $request->all());
                    if ($request->status == config('app.api.hbl.status.success_status') && $result) {

                        $detail = [
                            'type'   => 'HBL',
                            'status' => $result->status
                        ];

                        PaymentDetail::_storing($check['entry'], $detail);
                        PaymentEntry::_deleting($check['entry']->uuid);

                        return redirect()->route('result.success', [PaymentSetup::_encrypting($check['entry']->setup->uuid, $check['entry']->uuid)]);
                    }
                    return redirect()->route('result.failed');
                }
                return redirect()->route('result.error', [$check['status']]);
            } else {
                return abort(404);
            }
            Logs::_set('HBL Payment Error for payment - trans-ref-invalid, Action: SaleTxn, Error' . json_encode($request->all()), 'error-hbl');
            return redirect()->route('result.error', ['trans-ref-invalid']);
        } catch (Exception $e) {
            return redirect()->route('result.failed')->with('error', 'Something went Wrong, Try Again');
        }
    }

    public function hblBackendResponse(Request $request)////OLD HBL FUNCTION
    {

        Storage::disk('local')->put('file.txt', json_encode($request->all()));
        
    }

    public function connect_ips($entry, $encrypt, $request){
        $connectips_config = config('app.addons.payment_options.IPS');
        $TXNID = $entry->uuid;
        $url = $connectips_config['gateway'];
        $txn_amount = ($entry->total*100);

        $data = [
            'MERCHANTID' => $connectips_config['merchant_id'],
            'APPID' => $connectips_config['app_id'],
            'APPNAME' => $connectips_config['app_name'],
            'TXNID' => $TXNID,
            'TXNDATE' => date("d-m-Y"),
            'TXNCRNCY' => $entry->currency,
            'TXNAMT' => $txn_amount,
            'REFERENCEID' => 'RMKS_'.$TXNID,
            'REMARKS' => 'RMKS_' . $TXNID,
            'PARTICULARS' => 'PART_' . $TXNID,
            'TOKEN' => 'TOKEN'
        ];

        $message = '';
        foreach ($data as $key => $v) {
            $message .= $key . '=' . $v . ',';
        }
        $token = $this->generateHash(substr($message, 0, -1), $connectips_config['password_creditor']);
        $data['TOKEN'] = $token;
        return view('frontend.pay.connect_ips', compact('data', 'url'));
    }

    public function connectips_verify(Request $request){
        try {
            $merchant_id = $request->merchantId;
            $app_id = $request->appId;
            $reference_id = $request->referenceId;
            $txn_amt = $request->txnAmt;
            $status = $request->status;
            $status_desc = $request->statusDesc;

            $requestData = [
                'merchant_id' => $merchant_id,
                'app_id' => $app_id,
                'reference_id' => $reference_id,
                'txn_amt' => $txn_amt
            ];

            $result = $this->check($requestData);
            $store = PaymentConnectIpsService::_create($requestData);
            if ($result['status'] == 'SUCCESS') {
                $entry = PaymentEntryService::_find($store->uuid);

                $detail = [
                    'type'   => 'IPS',
                    'status' => $store->status
                ];
                PaymentDetailService::_storing($entry, $detail);
                PaymentEntryService::_update_new_entry($entry->uuid);

                return redirect()->route('result.success', [PaymentSetup::_encrypting($entry->setup->uuid, $entry->uuid)]);
            }
        } catch (Exception $e) {
            return redirect()->route('result.failed')->with('error', 'Something went Wrong, Try Again');
        }
        return redirect()->route('result.failed');
    }

    public  function check($requestData)
    {
        $connectips_config = config('app.addons.payment_options.IPS');

        $password = $connectips_config['password'];
        $amount = $requestData['txn_amt'];
        $referenceId = $requestData['reference_id'];
        $token = 'token';
        $data = [
            'MERCHANTID' =>  $requestData['merchant_id'],
            'APPID' =>  $requestData['app_id'],
            'REFERENCEID' =>  $requestData['reference_id'],
            'TXNAMT' => $amount . '00'
        ];
        $message = '';
        foreach ($data as $key => $v) {
            $message .= $key . '=' . $v . ',';
        }
        $token = $this->generateHash(substr($message, 0, -1), $connectips_config['password_creditor']);
        $requestData = [
            'MERCHANTID' => $requestData['merchant_id'],
            'APPID' =>  $requestData['app_id'],
            'REFERENCEID' => $requestData['reference_id'],
            'TXNAMT' => $amount . '00',
            'token' => $token
        ];
        $requestData['token'] = $token;
        $client = new Client();
        $response = $client->request(
            'POST',
            $connectips_config['validation_url'],
            [
                'headers' => ['Content-Type' => 'application/json', 'Accept' => 'application/json'],
                'auth' => [$requestData['app_id'], $password],
                'body' => json_encode($requestData)

            ]
        );
        $result =   json_encode(json_decode($response->getBody()));
        return $result;
    }

    public function generateHash($string, $password)
    {
        $token = null;
        $certificate = file_get_contents(base_path("CREDITOR.pfx"));

        if (openssl_pkcs12_read($certificate, $cert_info, $password)) {
            $private_key = openssl_pkey_get_private($cert_info['pkey']);
            if (openssl_sign($string, $signature, $private_key, 'sha256WithRSAEncryption')) {
                $token = base64_encode($signature);
            }
        }

        return $token;
    }
}
