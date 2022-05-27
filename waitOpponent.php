<?php

require 'vendor/autoload.php';
require 'src/Components/checkEnd.php';

session_name('TicTacToe');
session_start();

$session_id = session_id() ?? null;

use TicTacToe\Utils;
use TicTacToe\Utils\RequestException;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

try {
    $room_key = filter_input(INPUT_GET, 'room_key', FILTER_SANITIZE_STRING);

    $valid = Utils\Hash::validateHash($room_key);

    $result['success'] = false;
    $result['connect'] = false;

    if (!$valid) {
        throw new RequestException('Código inválido!');
    }

    $db = new Utils\Database('matches');
    $game = $db->select(['col' => 'hash', 'val' => $room_key])->fetch();

    if (empty($game) || empty($session_id)) {
        throw new RequestException('Jogo finalizado!');
    }

    if ($game['x'] != $session_id && $game['o'] != $session_id) {
        throw new RequestException('Você não é um participante!');
    }

    $ended = checkEnd($game);

    if ($ended) {
        $db->delete(['col' => 'hash', 'val' => $room_key]);
    }

    $result['dados']['vez'] = true;
    for ($i = 1; $i < 10; $i++) {
        $result['dados'][$i] = $game[$i];
        if ($game[$i] !== null) {
            $result['dados']['vez'] = !$result['dados']['vez'];
        }
    }

    if (!empty($game['o'])) {
        $result['connect'] = true;
    }

    $result['success'] = true;
    $result['code'] = 200;
} catch (RequestException $e) {
    $code = $e->getCode();
    $result['code'] = ($code === 0) ? 400 : $code;
    $result['message'] = $e->getMessage();
} finally {
    echo json_encode($result);
}
