<?php

namespace App\Helpers\HBLPayment;

use Carbon\Carbon;
use App\Helpers\SecurityData;
use App\Helpers\ActionRequest;
use GuzzleHttp\Exception\GuzzleException;

class Inquiry extends ActionRequest
{
    /**
     * @throws GuzzleException
     */
    public function Execute($officeId, $orderNo): string
    {
        $now = Carbon::now();

        $request = [
            "apiRequest" => [
                "requestMessageID" => $this->Guid(),
                "requestDateTime" => $now->utc()->format('Y-m-d\TH:i:s.v\Z'),
                "language" => "en-US",
            ],
            "advSearchParams" => [
                "controllerInternalID" => null,
                "officeId" => [
                    $officeId
                ],
                "orderNo" => [
                    "$orderNo"
                ],
                "invoiceNo2C2P" => null,
                "fromDate" => "0001-01-01T00:00:00",
                "toDate" => "0001-01-01T00:00:00",
                "amountFrom" => null,
                "amountTo" => null
            ],
        ];

        $stringRequest = json_encode($request);

        //third-party http client https://github.com/guzzle/guzzle
        $response = $this->client->post('api/1.0/Inquiry/transactionList', [
            'headers' => [
                'Accept' => 'application/json',
                'apiKey' => SecurityData::$AccessToken,
                'Content-Type' => 'application/json; charset=utf-8'
            ],
            'body' => $stringRequest
        ]);

        return $response->getBody()->getContents();
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function ExecuteJose($officeId, $orderNo): string
    {
        $now = Carbon::now();
        
        $request = [
            "apiRequest" => [
                "requestMessageID" => $this->Guid(),
                "requestDateTime" => $now->utc()->format('Y-m-d\TH:i:s.v\Z'),
                "language" => "en-US",
            ],
            "advSearchParams" => [
                "controllerInternalID" => null,
                "officeId" => [
                    $officeId
                ],
                "orderNo" => [
                    $orderNo
                ],
                "invoiceNo2C2P" => null,
                "fromDate" => "0001-01-01T00:00:00",
                "toDate" => "0001-01-01T00:00:00",
                "amountFrom" => null,
                "amountTo" => null
            ],
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
        $response = $this->client->post('api/1.0/Inquiry/transactionList', [
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