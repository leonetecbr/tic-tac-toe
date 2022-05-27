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

    // Se a hash não bater com os padrões
    if (!$valid) {
        throw new RequestException('Código inválido!');
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
    if ($game['x'] != $session_id && $game['o'] != $session_id) {
        throw new RequestException('Você não é um participante!');
    }

    $ended = checkEnd($game);

    // Verifica se a partida em questão já foi encerrada
    if ($ended) {
        // Se já encerrou é deletada do banco de dados
        $db->delete(['col' => 'hash', 'val' => $room_key]);
        throw new RequestException('A partida terminou ou expirou!');
    }

    // Determina de quem é vez
    $result['dados']['vez'] = true;
    for ($i = 1; $i < 10; $i++) {
        $result['dados'][$i] = $game[$i];
        if ($game[$i] !== null) {
            $result['dados']['vez'] = !$result['dados']['vez'];
        }
    }

    // Verifica se o outro jogador já está conectado
    if (!empty($game['o'])) {
        $result['connect'] = true;
    }

    $result['success'] = true;
    $result['code'] = 200;
} catch (RequestException $e) {
    $code = $e->getCode();
    $result['code'] = ($code === 0) ? 400 : $code;
    $result['message'] = $e->getMessage();
} catch (Exception $e){
    $result['code'] = 500;
    $result['message'] = 'Erro interno!';
} finally {
    echo json_encode($result);
}
