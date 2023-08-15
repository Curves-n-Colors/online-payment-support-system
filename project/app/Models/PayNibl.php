<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Notification;

use App\Notifications\SendPaymentErrorLog;
use App\Models\Logs;

class PayNibl extends Model
{
	// IPayAPI

	public $MerchantId_USD 	= '405635000006446';
	public $Password_USD   	= '123456789';
	public $Key_USD 		= 'MzQ0MDU2MzUwMDAwMDY0NDY0N2YwNDM0Yy0wMGNhLTQwOTEtOGM4NC1iODcxOWNlMWJlN';

	public $MerchantId_NPR 	= '405635000006446';
	public $Password_NPR   	= '123456789';
	public $Key_NPR 		= 'MzQ0MDU2MzUwMDAwMDY0NDY0N2YwNDM0Yy0wMGNhLTQwOTEtOGM4NC1iODcxOWNlMWJlN';

	public $API_URL 	 	= "https://202.63.245.71:30090/ipg/servlet_exppear";
	public $iPay_URL 	 	= 'https://202.63.245.71:30090/ipg/servlet_exppay';
	public $Version 	 	= '1.00';
	public $LanguageCode 	= 'eng';

	public $MerchantId	 	= null;
	public $Password	 	= null;
	public $Key	 			= null;

	public function _curl($Invoice)
	{
		$options = array(
			CURLOPT_URL            => $this->API_URL,
			CURLOPT_POST           => true,
			CURLOPT_POSTFIELDS     => $this->_params($Invoice),
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSL_VERIFYHOST => false
		);

		$curl = curl_init();
		curl_setopt_array($curl, $options);
		$response = curl_exec($curl);
		curl_close($curl);

		$responseArr = explode("&", $response);
		$response = array();

		foreach ($responseArr as $value) {
			$tmpArr = explode("=", $value);
			if (sizeof($tmpArr) > 1) $response[strtoupper($tmpArr[0])] = $tmpArr[1];
		}

		return $response;
	}

	public function _params($Invoice)
	{
		$sHash = $this->_hash($Invoice);
		$ptInvoice = $this->_bin2hex($Invoice);
		return 'VERSION=' . $this->Version . '&PWD=' . $this->Password . '&MERCHANTID=' . $this->MerchantId . '&KEY=' . $this->Key . '&HASH=' . $sHash . '&PTINVOICE=' . $ptInvoice;
	}

	public function _hash($Invoice)
	{
		return hash('sha256', $Invoice . $this->Key, false);
	}

	public function _bin2hex($Invoice)
	{
		return bin2hex($Invoice);
	}

	public function _hextostr($hex)
	{
		$str = '';
		for ($i = 0; $i < strlen($hex) - 1; $i += 2) {
			$str .= chr(hexdec($hex[$i] . $hex[$i + 1]));
		}
		return $str;
	}

	public function _xmlelements($string)
	{
		return new \SimpleXMLElement($this->_hextostr($string));
	}

	public function _set($CurrencyCode)
	{
		if (!in_array($CurrencyCode, ['USD', 'NPR'])) {
			return ['status' => 'trans-verfiy-failed'];
		}

		if ($CurrencyCode == 'USD') {
			$this->MerchantId	= $this->MerchantId_USD;
			$this->Password 	= $this->Password_USD;
			$this->Key 			= $this->Key_USD;
		} else {
			$this->MerchantId 	= $this->MerchantId_NPR;
			$this->Password 	= $this->Password_NPR;
			$this->Key 			= $this->Key_NPR;
		}
	}

	public function _failed($entry, $action, $response)
	{
		$notify = [
			'uuid'   => $entry->uuid,
			'client_id' => $entry->client->id,
			'client' => $entry->client->name,
			'email'  => $entry->email,
			'title'  => $entry->title,
			'action' => $action,
			'error_log' => json_encode($response)
		];
		Notification::route('mail', env('PRIMARY_MAIL'))->notify(new SendPaymentErrorLog($notify));
		Logs::_set('NIBL Payment Error for payment title - ' . $notify['title'] . ', Action: ' . $notify['action'] . ' Error' . $notify['error_log'], 'error-nibl');
		return ['status' => 'trans-verfiy-failed'];
	}

	public function process($entry)
	{
		$this->_set($entry->currency);

		$Action 		= "SaleTxn";
		$ReturnURL 		= route('pay.nibl.confirm');
		$MerRefID 		= str_pad(time(), 20, 0, STR_PAD_LEFT); // str_pad($entry->id, 20, 0, STR_PAD_LEFT); // rand(1000, 99999);
		$TxnAmount 		= number_format($entry->total, 2, '.', '');
		$CurrencyCode 	= $entry->currency;
		$MerVar1 		= $entry->uuid;
		$MerVar2 		= config('app.addons.private_key');
		$MerVar3		= '';
		$MerVar4 		= '';
		$sItemList		= '';

		$Invoice =  "<req>" .
			"<mer_id>" . $this->MerchantId . "</mer_id>" .
			"<mer_txn_id>" . $MerRefID . "</mer_txn_id>" .
			"<action>" . $Action . "</action>" .
			"<txn_amt>" . $TxnAmount . "</txn_amt>" .
			"<cur>" . $CurrencyCode . "</cur>" .
			"<lang>" . $this->LanguageCode . "</lang>" .
			"<ret_url>" . $ReturnURL . "</ret_url>";

		if (!empty($MerVar1)) {
			$Invoice .= "<mer_var1>" . $MerVar1 . "</mer_var1>";
		}
		if (!empty($MerVar2)) {
			$Invoice .= "<mer_var2>" . $MerVar2 . "</mer_var2>";
		}
		if (!empty($MerVar3)) {
			$Invoice .= "<mer_var3>" . $MerVar3 . "</mer_var3>";
		}
		if (!empty($MerVar4)) {
			$Invoice .= "<mer_var4>" . $MerVar4 . "</mer_var4>";
		}
		if (!empty($sItemList)) {
			$Invoice .= "<item_list>" . $sItemList . "</item_list>";
		}

		$Invoice .= "</req>";

		$response = $this->_curl($Invoice);

		if (strcasecmp($response['ERROR_CODE'], '000') == 0) {
			// $sHash1 = hash('sha256', $response['PTRECEIPT'].$this->Key,false);
			// && strcasecmp($response['HASH'], $sHash1) == 0

			$xml = $this->_xmlelements($response['PTRECEIPT']);
			return array('status' => 200, 'txn_uuid' => (string) $xml->txn_uuid, 'iPay_URL' => $this->iPay_URL);
		}

		return $this->_failed($entry, 'payment', $response);
	}

	public function verify($entry, $params)
	{
		$this->_set($entry->currency);

		$MerRefID = $params['mer_ref_id'];
		$TxnUUID  = $params['TXN_UUID'];
		$Action   = 'saleTxnVerify';

		$Invoice =  "<req>" .
			"<mer_id>" . $this->MerchantId . "</mer_id>" .
			"<mer_txn_id>" . $MerRefID . "</mer_txn_id>" .
			"<txn_uuid>" . $TxnUUID . "</txn_uuid>" .
			"<action>" . $Action . "</action>" .
			"</req>";

		$response = $this->_curl($Invoice);

		if (strcasecmp($response['ERROR_CODE'], '000') == 0) {
			// $sHash1 = hash('sha256', $response['PTRECEIPT'].$this->Key,false);
			// && strcasecmp($response['HASH'], $sHash1) == 0

			$xml = $this->_xmlelements($response['PTRECEIPT']);
			return array_merge((array) $xml, array('status' => 200));
		}

		return $this->_failed($entry, 'payment', $response);
	}

	public function refund($detail, $params)
	{
		$this->_set($detail->currency);

		$MerRefID 		= $params['mer_ref_id'];
		$IpgtxnID 		= $params['ipg_txn_id'];
		$reundAmt 		= number_format($params['amount'], 2, '.', '');
		$Action   		= $params['is_full'] ? 'VoidTransaction' : 'RefundTransaction';

		$Invoice =  "<req>" .
			"<mer_id>" . $this->MerchantId . "</mer_id>" .
			"<mer_txn_id>" . $MerRefID . "</mer_txn_id>" .
			"<ipg_txn_id>" . $IpgtxnID . "</ipg_txn_id>";

		if (!$params['is_full']) {
			$Invoice .= "<refund_amt>" . $reundAmt . "</refund_amt>";
		}

		$Invoice .= "<action>" . $Action . "</action>" .
			"</req>";

		$response = $this->_curl($Invoice);

		if (strcasecmp($response['ERROR_CODE'], '000') == 0) {

			$xml = $this->_xmlelements($response['PTRECEIPT']);
			return array_merge((array) $xml, array('status' => 200));
		}

		return $this->_failed($detail, 'refund', $response);
	}
}
