<?php

namespace TicTacToe\Utils;

/**
 * Classe responsável por validar a verificação robótica do ReCaptcha
 */
class ReCaptcha
{
	private $response;
	private $ip;
	private $secret;

	/**
	 * Preenche as variáveis nescessárias para a verificação
	 * @param string $response
	 * @param string $type
	 */
	public function __construct($response, $type = 'v3')
	{
		$this->ip = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['REMOTE_ADDR'];
		if ($type == 'v3') {
			$this->secret = $_ENV['SECRET_RECAPTCHA_V3'];
		} else {
			$this->secret =  $_ENV['SECRET_RECAPTCHA_V2'];
		}
		$this->response = $response;
	}

	/**
	 * Faz a consulta na API
	 * @return array
	 */
	private function getApi()
	{
		$dados = array(
			"secret" => $this->secret,
			"response" => $this->response,
			"remoteip" => $this->ip
		);
		$curlReCaptcha = curl_init();
		curl_setopt($curlReCaptcha, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
		curl_setopt($curlReCaptcha, CURLOPT_POST, true);
		curl_setopt($curlReCaptcha, CURLOPT_POSTFIELDS, http_build_query($dados));
		curl_setopt($curlReCaptcha, CURLOPT_RETURNTRANSFER, true);
		$result = json_decode(curl_exec($curlReCaptcha), true);
		curl_close($curlReCaptcha);
		return $result;
	}

	/**
	 * Faz a validação usando a API V3
	 * @param float $min 0 a 1
	 * @return boolean (false caso não seja um robô)
	 */
	public function isOrNotV3($min = 0.6)
	{
		$response = $this->getApi();
		if (!empty($response['success'])) {
			if ($response['success'] == 1 /*&& $response['hostname']===$_SERVER['HTTP_HOST']*/ && $response['score'] >= $min) {
				return false;
			} else {
				return true;
			}
		} else {
			return true;
		}
	}

	/**
	 * Faz a validação usando a API V2
	 * @return boolean (false caso não seja um robô)
	 */
	public function isOrNotV2()
	{
		$response = $this->getApi();
		if (!empty($response['success'])) {
			if ($response['success'] == 1 /*&& $response['hostname']===$_SERVER['HTTP_HOST']*/) {
				return false;
			} else {
				return true;
			}
		} else {
			return true;
		}
	}
}
