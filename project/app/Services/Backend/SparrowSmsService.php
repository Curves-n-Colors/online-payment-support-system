<?php

namespace App\Services\Backend;

class SparrowSmsService
{

    public function _send($params)
	{
		$sparrow = config('app.addons.sparrow');
		$args = http_build_query([
			'token' => $sparrow['token'],
			'from'  => $sparrow['identity'],
			'to'    => $params['to'],
			'text'  => $params['msg']
		]);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $sparrow['request_url']);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		$response = ["response" => curl_exec($ch), "status" => curl_getinfo($ch, CURLINFO_HTTP_CODE)];
		curl_close($ch);

		// save this data into database for future reference

		return $response;
	}

	public function _credit()
	{
		$sparrow = config('app.addons.sparrow');
		return file_get_contents($sparrow['credit_check_url']);
	}


}
