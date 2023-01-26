<?php

namespace App\Http\Controllers\Frontend;

use DB;
use Carbon\Carbon;

use App\Models\Logs;
use App\Models\PayNibl;
use Illuminate\Support\Str;
use App\Models\PayKhalti;
use App\Models\PaymentHBL;
use App\Models\PaymentNibl;
use App\Models\PaymentEntry;
use App\Models\PaymentSetup;
use Illuminate\Http\Request;

use App\Models\PaymentDetail;
use App\Models\PaymentKhalti;
use App\Http\Requests\PayRequest;
use App\Http\Requests\NiblRequest;
use App\Helpers\HBLPayment\Payment;
use App\Http\Controllers\Controller;
use App\Models\HblPaymentResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;


class PayController extends Controller
{
    public function index(Request $request, $encrypt)
    {
        $check = PaymentSetup::_validating($encrypt);
        if ($check['status'] == 200) {
            $entry = $check['entry'];
            $detail = $check['detail'];

            if ($entry) {
                return view('frontend.pay.index', compact('encrypt', 'entry'));
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
            'amount'    => (int) ($entry->total * 100),
        ];

        PaymentKhalti::_create($entry->uuid, $transaction);

        $pay = new PayKhalti();
        $khalti = $pay->verify($transaction);

        if ($khalti['status'] == 200) {

            $model = PaymentKhalti::_update($entry->uuid, $khalti['response']['idx']);

            $detail = [
                'type'   => 'KHALTI',
                'status' => $model->status
            ];

            PaymentDetail::_storing($entry, $detail);
            PaymentEntry::_deleting($entry->uuid);
            return redirect()->route('result.success', [$encrypt]);
        }

        PaymentKhalti::_update($entry, null);
        return redirect()->route('result.failed');
    }

    public function hbl_pay($entry, $encrypt, $request)
    {

        $invoiceNo = str_pad(Carbon::now()->timestamp, 20, 0, STR_PAD_LEFT);

        $pay = new Payment(
            $orderNo = $invoiceNo,
            $amount = $entry['total'],
            $productDescription =  $entry['title'],
            $amountText = str_pad($entry['total'] * 100, 12, 0, STR_PAD_LEFT),
            $currencyCode = $entry['currency'],
            $officeId = config('app.addons.payment_options.hbl.merchant_id'),
            $encryptCode = $encrypt, 
        );
        $response = $pay->ExecuteJose();

        if($response){
            HblPaymentResponse::_save_response($response,$entry['uuid']);
        }

        $response = json_decode($response, true);
        
        return  $response;
        return redirect($response['data']['paymentPage']['paymentPageURL']);

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
}
