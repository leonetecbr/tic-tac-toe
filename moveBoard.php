<?php

require 'vendor/autoload.php';
require 'src/Components/checkEnd.php';

use TicTacToe\Utils;
use TicTacToe\Utils\RequestException;
use Dotenv\Dotenv;

try {
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

	if (!$valid) {
		throw new RequestException('Código inválido!');
	}

    if ($casa > 9 || $casa < 1) {
        throw new RequestException('Casa inválida!');
    }

	$db = new Utils\Database('matches');
	$game = $db->select(['col' => 'hash', 'val' => $room_key])->fetch();

	if (empty($game) || empty($session_id)) {
		throw new RequestException('Código inválido!');
	}

	if (($be && $game['x'] != $session_id) || (!$be && $game['o'] != $session_id)) {
        throw new RequestException('Sem permissão!', 401);
    }

	$ended = checkEnd($game);

	if ($ended) {
		$db->delete(['col' => 'hash', 'val' => $room_key]);
		throw new RequestException('A partida terminou ou expirou!');
	}

	if ($game[$casa] !== null) {
		throw new RequestException('Casa já marcada!');
	}

    $marked = 0;
    for ($i = 1; $i <= 9; $i++){
        if ($game[$i] !== null){
            $marked++;
        }
    }

    $vez = ($marked % 2 === 0);

    if ($vez !== $be){
        throw new RequestException('Aguarde sua vez!', 403);
    }

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
