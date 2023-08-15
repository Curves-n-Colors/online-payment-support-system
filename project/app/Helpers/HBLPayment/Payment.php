<?php

namespace App\Helpers\HBLPayment;

use Carbon\Carbon;
// use App\Helpers\SecurityData;
use App\Helpers\SecurityDataPROD as SecurityData;
use App\Helpers\ActionRequest;
use GuzzleHttp\Exception\GuzzleException;


class Payment extends ActionRequest
{
    private ?string $orderNo = null;
    private ?string $amount = null;

    public function __construct($orderNo, $amount, $productDescription, $amountText, $currencyCode, $officeId, $encryptCode)
    {
        $this->orderNo = $orderNo;
        $this->amount = $amount;
        $this->productDescription =  $productDescription;
        $this->amountText = $amountText;
        $this->currencyCode = $currencyCode;
        $this->officeId = $officeId;
        $this->encryptCode = $encryptCode;
        parent::__construct();
    }
    /**
     * @throws GuzzleException
     */
    public function Execute(): string
    {
        $now = Carbon::now();
        
        $request = [
            "apiRequest" => [
                "requestMessageID" => $this->Guid(),
                "requestDateTime" => $now->utc()->format('Y-m-d\TH:i:s.v\Z'),
                "language" => "en-US",
            ],//required
            "officeId" => "{$this->officeId}",//required😎
            "orderNo" => "{$this->orderNo}",//required😎
            "productDescription" => "{$this->productDescription}",//required😎
            "paymentType" => "CC",//required😎
            "paymentCategory" => "ECOM",//Optional😎
            "recurringPaymentDetails" => [
                "rppFlag" => "N",//required (Y/N)
            ],//not imp😎
            "installmentPaymentDetails" => [
                "ippFlag" => "N",//required (Y/N)
            ],//defult😎
            "mcpFlag" => "N",//required (Y/N)😎
            "request3dsFlag" => "N",//Not imp😎
            "transactionAmount" => [
                "amountText" => "{$this->amountText}",
                "currencyCode" => "{$this->currencyCode}",
                "decimalPlaces" => 2,
                "amount" => $this->amount
            ],//required😎
            "notificationURLs" => [
                "confirmationURL" => route('result.success', $this->encryptCode),
                "failedURL" => route('result.failed'),
                "cancellationURL" => route('result.cancellation'),
                "backendURL" => route('pay.hbl.proceed'),
            ],//required😎
            "autoRedirectDelayTimer" => 5,//😎
            "channelCode" => "WEBPAY"//😎
        ];
        $stringRequest = json_encode($request);
        
        
        //third-party http client https://github.com/guzzle/guzzle
        $response = $this->client->post('api/1.0/Payment/prePaymentUI', [
            'headers' => [
                'Accept' => 'application/json',
                'apiKey' => SecurityData::$AccessToken,
                'Content-Type' => 'application/json'
            ],
            'body' => $stringRequest
        ]);
        

        return $response->getBody()->getContents();
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function ExecuteJose()
    {
        $now = Carbon::now();
        // $orderNo = $this->orderNo;
        // $amount = round((double)$this->amount,2);
        // $amount_text = $this->AmountText($amount);

        $request = [
            "apiRequest" => [
                "requestMessageID" => $this->Guid(),
                "requestDateTime" => $now->utc()->format('Y-m-d\TH:i:s.v\Z'),
                "language" => "en-US",
            ],
            "officeId" => "{$this->officeId}",
            "orderNo" => "{$this->orderNo}",
            "productDescription" => "{$this->productDescription}",
            "paymentType" => "CC",
            "paymentCategory" => "ECOM",            
            "storeCardDetails" => [
                "storeCardFlag" => "N",
                "storedCardUniqueID" => "{{guid}}"
            ],
            "installmentPaymentDetails" => [
                "ippFlag" => "N",
                "installmentPeriod" => 0,
                "interestType" => null
            ],
            "mcpFlag" => "N",
            "request3dsFlag" => "N",
            "transactionAmount" => [
                "amountText" => "{$this->amountText}",
                "currencyCode" => "{$this->currencyCode}",
                "decimalPlaces" => 2,
                "amount" => $this->amount
            ],
            "notificationURLs" => [
                "confirmationURL" => route('result.success', $this->encryptCode),
                "failedURL" => route('result.failed'),
                "cancellationURL" => route('result.cancellation'),
                "backendURL" => route('result.backend')
            ],
            "deviceDetails" => [
                "browserIp" => "1.0.0.1",
                "browser" => "Postman Browser",
                "browserUserAgent" => "PostmanRuntime/7.26.8 - not from header",
                "mobileDeviceFlag" => "N"
            ],
            "purchaseItems" => [
                [
                    "purchaseItemType" => "ticket",
                    "referenceNo" => "2322460376026",
                    "purchaseItemDescription" => "Bundled insurance",
                    "purchaseItemPrice" => [
                        "amountText" => "000000000100",
                        "currencyCode" => "NPR",
                        "decimalPlaces" => 2,
                        "amount" => 1
                    ],
                    "subMerchantID" => "string",
                    "passengerSeqNo" => 1
                ]
            ],
            "customFieldList" => [
                [
                    "fieldName" => "TestField",
                    "fieldValue" => "This is test"
                ]
            ]
        ];

        $payload = [
            "request" => $request,
            "iss" => SecurityData::$AccessToken,
            "aud" => "PacoAudience",
            "CompanyApiKey" => SecurityData::$AccessToken,
            "iat" => $now->unix(),
            "nbf" => $now->unix(),
            "exp" => $now->addHour()->unix(),
        ];

        $stringPayload = json_encode($payload);
        $signingKey = $this->GetPrivateKey(SecurityData::$MerchantSigningPrivateKey);
        $encryptingKey = $this->GetPublicKey(SecurityData::$PacoEncryptionPublicKey);
        $body = $this->EncryptPayload($stringPayload, $signingKey, $encryptingKey);
        
        //third-party http client https://github.com/guzzle/guzzle
        $response = $this->client->post('api/1.0/Payment/prePaymentUI', [
            'headers' => [
                'Accept' => 'application/jose',
                'CompanyApiKey' => SecurityData::$AccessToken,
                'Content-Type' => 'application/jose; charset=utf-8'
            ],
            'body' => $body
        ]);
        
        $token = $response->getBody()->getContents();
        $decryptingKey = $this->GetPrivateKey(SecurityData::$MerchantDecryptionPrivateKey);
        $signatureVerificationKey = $this->GetPublicKey(SecurityData::$PacoSigningPublicKey);

        return $this->DecryptToken($token, $decryptingKey, $signatureVerificationKey);
    }
    
}