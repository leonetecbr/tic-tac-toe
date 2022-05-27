<?php

namespace TicTacToe\Utils;

/**
 * Classe responsável por validar a verificação robótica do ReCaptcha
 */
class ReCaptcha
{
    /**
     * Token de resposta recebido do ReCaptcha no front-end
     * @var string
     */
	private $response;

    /**
     * IP do usuário
     * @var string
     */
	private $ip;

    /**
     * Chave secreta do ReCaptcha
     * @var string
     */
	private $secret;

	/**
	 * Preenche as variáveis necessárias para a verificação
	 * @param string $response
	 * @param string $type
	 */
	public function __construct(string $response, string $type = 'v3')
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
	private function getApi(): array
    {
		$dados = array(
			"secret" => $this->secret,
			"response" => $this->response,
			"remoteip" => $this->ip
		);
		$curlReCaptcha = curl_init();
        $options = [
            CURLOPT_URL => "https://www.google.com/recaptcha/api/siteverify",
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($dados),
            CURLOPT_RETURNTRANSFER => true
        ];
		curl_setopt_array($curlReCaptcha, $options);
		$result = json_decode(curl_exec($curlReCaptcha), true);
		curl_close($curlReCaptcha);
		return $result;
	}

	/**
	 * Faz a validação usando a API V3
	 * @param float $min 0 a 1
	 * @return boolean (false caso não seja um robô)
	 */
	public function isOrNotV3(float $min = 0.6): bool
    {
		$response = $this->getApi();
		if (!empty($response['success'])) {
			if ($response['success'] == 1 && $response['hostname'] === $_SERVER['SERVER_NAME'] && $response['score'] >= $min) {
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
	public function isOrNotV2(): bool
    {
		$response = $this->getApi();
		if (!empty($response['success'])) {
			if ($response['success'] == 1 && $response['hostname'] === $_SERVER['SERVER_NAME']) {
				return false;
			} else {
				return true;
			}
		} else {
			return true;
		}
	}
}
