<?php

return [

	'public_key'  => 'MDwjgtVspt58haKebsV7mNvAQIMRwnN3',

	'private_key' => 'ySgpzBm4kIwExaCfIAgt7gHwwiqEWBk0ipCgEpXIgZosQV6ik0',

	'ref_code_prefix' => 'CLB',

	'currency_code' => [
		'NPR', 'USD'
	],

	'recurring_type' => [
		1  => 'ONETIME',
		2  => 'WEEKLY',
		3  => 'MONTHLY',
		4  => 'QUARTERLY',
		5  => 'YEARLY'
	],

	'type_recurring' => [
		'ONETIME'  => 1,
		'WEEKLY'   => 2,
		'MONTHLY'  => 3,
		'QUARTERLY'=> 4,
		'YEARLY'   => 5
	],

    'sparrow' => [
        'token'            => 'bqcdhRTETd8R8j0jygXm',
        'identity'         => 'ThamesIntl',
        'request_url'      => 'http://api.sparrowsms.com/v2/sms/',
        'credit_check_url' => 'http://api.sparrowsms.com/v2/credit/?token=bqcdhRTETd8R8j0jygXm'
    ],

	'payment_status' => [
		0  => 'PENDING',
		1  => 'CANCELLED',
		2  => 'CONFIRMED',
		3  => 'INVALID',
		4  => 'REFUNDED',
		10 => 'COMPLETED'
	],

	'status_payment' => [
		'PENDING'    => 0,
		'CANCELLED'  => 1,
		'CONFIRMED'  => 2,
		'INVALID'    => 3,
		'REFUNDED'   => 4,
		'COMPLETED'  => 10
	],

	'payment_status_code' => [
		'P'    => 0,//PENDING
		'C'    => 1,//CANCELED
		'F'    => 2,//FAILED
		'PCPS' => 3,//INCOMPLETE
		'A'    => 10//APPROVED
	],

	'payment_status_info' => [
		0  => 'PENDING',
		1  => 'CANCELED',
		2  => 'FAILED',
		3  => 'INCOMPLETE',
		10 => 'APPROVED',
	],

	'payment_options' => [

		'hbl' => [
			'code'  			=> 'HBL',
			'name'  			=> 'Himalayan Bank Ltd - Payment Gateway',
			'title' 			=> 'Credit Card - HBL',
			'non_secure'        => 'N',
			// 'merchant_id'       => '9103338075',
			'merchant_id'       => '9103332177',
			'merchant_name'     => 'CLIMBALAYA_TREKS_AND_EXPEDITION_ECOM_NPL',
			'secret_key'        => 'U9LABZ33POUA3WMG6BWNZ8DVBGTNSKXR',
			'request_url'       => 'https://hblpgw.2c2p.com/HBLPGW/Payment/Payment/Payment',
			'test_request_url'  => 'https://hbl.pgw/payment',
			'currency' => [
				'NPR' 	=> 'NPR',
				'USD' 	=> 'USD'
			],
			'currency_code'     => [
				'USD' 	=> 840,
				'NPR' 	=> 524
			],
			'url_encryption_key' 	=> 'Lrot3yy3CQkJnBipexF4BiKD5ybWsfKjWzAfG6clilSYw0WWuh78ITJNUplA9Dzo',
			'frontend_response_uri' => '/hbl-payment/{url_encryption_key}',
			'backend_response_uri'  => '/hbl-payment-response',

			'status' => [
				'success_status'    => 'RS',
				'payment_status'    => [
					'AP' => 'Approved(Paid)',
					'SE' => 'Settled',
					'VO' => 'Voided (Canceled)',
					'DE' => 'Declined by the issuer Host',
					'FA' => 'Failed',
					'PE' => 'Pending',
					'EX' => 'Expired',
					'RE' => 'Refunded',
					'RS' => 'Ready to Settle',
					'AU' => 'Authenticated',
					'IN' => 'Initiated',
					'FP' => 'Fraud Passed',
					'PA' => 'Paid (Cash)',
					'MA' => 'Matched (Cash)',
				],
				'response_code'     => [
					'00' => 'Approved (transaction is successfully paid)',
					'01' => 'Refer to Card Issuer',
					'02' => 'Refer to Issuer\'s Special Conditions',
					'03' => 'Invalid Merchant ID',
					'04' => 'Pick Up Card',
					'05' => 'Do Not Honour',
					'06' => 'Error',
					'07' => 'Pick Up Card, Special Conditions',
					'08' => 'Honour with ID',
					'09' => 'Request in Progress',
					'10' => 'Partial Amount Approved',
					'11' => 'Approved VIP',
					'12' => 'Invalid Transaction',
					'13' => 'Invalid Amount',
					'14' => 'Invalid Card Number',
					'15' => 'No Sun Issuer',
					'16' => 'Approved, Update Track 3',
					'17' => 'Customer Cancellation',
					'18' => 'Customer Dispute',
					'19' => 'Re-enter Transaction',
					'20' => 'Invalid Response',
					'21' => 'No Action Taken',
					'22' => 'Suspected Malfunction',
					'23' => 'Unacceptable Transaction Fee',
					'24' => 'File Update not Supported by Receiver',
					'25' => 'Unable to Locate Record on File',
					'26' => 'Duplicate File Update Record',
					'27' => 'File Update Field Edit Error',
					'28' => 'File Update File Locked Out',
					'29' => 'File Update not Successful',
					'30' => 'Format Error',
					'31' => 'Bank not Supported by Switch',
					'32' => 'Completed Partially',
					'33' => 'Expired Card - Pick Up',
					'34' => 'Suspected Fraud - Pick Up',
					'35' => 'Contact Acquirer - Pick Up',
					'36' => 'Restricted Card - Pick Up',
					'37' => 'Call Acquirer Security - Pick Up',
					'38' => 'Allowable PIN Tries Exceeded',
					'39' => 'No Credit Account',
					'40' => 'Requested Function not Supported',
					'41' => 'Lost Card - Pick Up',
					'42' => 'No Universal Amount',
					'43' => 'Stolen Card - Pick Up',
					'44' => 'No Investment Account',
					'45' => 'Settlement Success',
					'46' => 'Settlement Fail',
					'47' => 'Reserved',
					'48' => 'Cancel Fail',
					'49' => 'No Transaction Reference Number',
					'50' => 'Host Down',
					'51' => 'Insufficient Funds',
					'52' => 'No Cheque Account',
					'53' => 'No Savings Account',
					'54' => 'Expired Card',
					'55' => 'Incorrect PIN',
					'56' => 'No Card Record',
					'57' => 'Trans. not Permitted to Cardholder',
					'58' => 'Transaction not Permitted to Terminal',
					'59' => 'Suspected Fraud',
					'60' => 'Card Acceptor Contact Acquirer',
					'61' => 'Exceeds Withdrawal Amount Limits',
					'62' => 'Restricted Card',
					'63' => 'Security Violation',
					'64' => 'Original Amount Incorrect',
					'65' => 'Exceeds Withdrawal Frequency Limit',
					'66' => 'Card Acceptor Call Acquirer Security',
					'67' => 'Hard Capture - Pick Up Card at ATM',
					'68' => 'Response Received Too Late',
					'69' => 'Reserved',
					'70' => 'Settle amount cannot more than authorized amount',
					'71' => 'Inquiry Record Not Exist',
					'72' => 'Reserved',
					'73' => 'Reserved',
					'74' => 'Rejected by Fraud',
					'75' => 'Allowable PIN Tries Exceeded',
					'76' => 'Invalid Credit Card Format',
					'77' => 'Invalid Expiry Date Format',
					'78' => 'Invalid Three Digits Format',
					'79' => 'Only Full Authentication Allowed',
					'80' => 'User Cancellation by closing Internet Browser',
					'81' => 'Corporate Card Blocked',
					'82' => 'Verify Request Data Failed',
					'83' => 'Merchant Currency Mismatched',
					'84' => 'Reserved',
					'85' => 'Reserved',
					'86' => 'Reserved',
					'87' => 'No Envelope Inserted',
					'88' => 'Unable to Dispense',
					'89' => 'Administration Error',
					'90' => 'Cut-off in Progress',
					'91' => 'Issuer or Switch is Inoperative',
					'92' => 'Financial Institution not Found',
					'93' => 'Trans Cannot be Completed',
					'94' => 'Duplicate Transmission',
					'95' => 'Reconcile Error',
					'96' => 'System Malfunction',
					'97' => 'Reconciliation Totals Reset',
					'98' => 'MAC Error',
					'99' => 'System Unavailable',
				],
				'fraud_code'        => [
					'00' => 'High possibility of no fraud',
					'86' => 'Merchant in whitelist(entry date : [[DDMMYY]])',
					'87' => 'PAN in whitelist(entry date : [[DDMMYY]])',
					'88' => 'Not Local IP Country',
					'89' => 'Bank Name not matched',
					'90' => 'Bank Country not matched',
					'91' => 'Exceeded over [[limit]] Txn limit of one IP using multiple PAN within 24 hours',
					'92' => 'Exceeded over [[limit]] PAN limit of inter non-3DS cards within 24 hours',
					'93' => 'Exceeded over [[limit]] PAN limit of inter 3DS cards within 24 hours',
					'94' => 'Exceeded over [[limit]] PAN limit of local non-3DS cards within 24 hours',
					'95' => 'Exceeded over [[limit]] PAN limit of local 3DS cards within 24 hours',
					'96' => 'BIN in black list(entry date : [[DDMMYY]])',
					'97' => 'IP in black list(entry date : [[DDMMYY]])',
					'98' => 'PAN in blacklist(entry date : [[DDMMYY]])',
					'99' => 'General Error : [[details]]'
				],
			],
		],

	],

	'errors' => [
		'link-inactive' 		=> 'Your payment link has been deactivated.',
		'link-invalid'      	=> 'Your payment link is invalid.',
		'link-unauthorised' 	=> 'Your payment link is unauthorised.',
		'link-expired' 			=> 'Your payment link has expired.',
		'trans-verfiy-failed' 	=> 'Your payment transaction verification failed.',
		'trans-cancel-pg' 		=> 'Your payment transaction has been cancelled from the payment gateway server',
		'trans-ref-invalid' 	=> 'Your payment reference is invalid',
		'trans-complete' 	    => 'Your payment was successful',
	]
];

/*

errors.100
=> this condition is triggered when a payment is successful and system deactivated the payment link
=> in pay.index route it is redirect as link deactived error
=> but in result.success it is considered as payment successful


https://unpkg.com/khalti-checkout-web@latest/dist/khalti-checkout.iffe.js
*/
