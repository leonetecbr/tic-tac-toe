<?php

require 'vendor/autoload.php';
require 'src/Components/checkEnd.php';

use TicTacToe\Utils;
use TicTacToe\Utils\RequestException;
use Dotenv\Dotenv;

try {
    // Se o método não for POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST'){
        throw new Utils\RequestException('Método não permitido!', 405);
    }

    session_name('TicTacToe');
    session_start();

    $session_id = session_id() ?? null;

    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();

    $room_key = filter_input(INPUT_POST, 'room_key', FILTER_SANITIZE_STRING);

    if (empty($room_key) || empty($_POST['position']) || empty($_POST['per'])){
        throw new RequestException('Faltam dados na solicitação!', 400);
    }

    $casa = intval($_POST['position']);
    $be = ($_POST['per'] === 'true');

    $valid = Utils\Hash::validateHash($room_key);

    $result['success'] = false;

    // Se a hash não bater com os padrões
	if (!$valid) {
		throw new RequestException('Código inválido!');
	}

    // Se o código da casa for fora da escala permitida
    if ($casa > 9 || $casa < 1) {
        throw new RequestException('Casa inválida!');
    }

	$db = new Utils\Database('matches');
	$game = $db->select(['col' => 'hash', 'val' => $room_key])->fetch();

    // Se o jogo não existe mais no banco de dados
    if (empty($game)) {
        throw new RequestException('Jogo finalizado!');
    }

    // Se o usuário não tem um identificar nos cookies
    if (empty($session_id)){
        throw new RequestException('Ative os cookies!');
    }

    // Verifica se a identificação presente os cookies é a mesma do banco de dados
	if (($be && $game['x'] != $session_id) || (!$be && $game['o'] != $session_id)) {
        throw new RequestException('Sem permissão!', 401);
    }

	$ended = checkEnd($game);

    // Verifica se a partida em questão já foi encerrada
	if ($ended) {
        // Se já encerrou é deletada do banco de dados
		$db->delete(['col' => 'hash', 'val' => $room_key]);
		throw new RequestException('A partida terminou ou expirou!');
	}

    // Verifica se a casa não já foi marcada
	if ($game[$casa] !== null) {
		throw new RequestException('Casa já marcada!');
	}

    // Conta quantas casas foram marcadas
    $marked = 0;
    for ($i = 1; $i <= 9; $i++){
        if ($game[$i] !== null){
            $marked++;
        }
    }

    // Determina de quem é a vez
    $vez = ($marked % 2 === 0);

    // Movimento fora da vez do usuário
    if ($vez !== $be){
        throw new RequestException('Aguarde sua vez!', 403);
    }

    // Marca a casa
	$db->update(['matches.' . $casa => intval($be)], 'hash = "' . $room_key . '"');
	$result['success'] = true;
	$result['code'] = 200;
} catch (RequestException $e) {
	$code = $e->getCode();
    $result['success'] = false;
	$result['code'] = ($code === 0) ? 400 : $code;
	$result['message'] = $e->getMessage();
} catch (\Exception $e) {
    die($e);
}
finally {
	echo json_encode($result);
}
