<?php

use TicTacToe\Utils;

$isRobot = true;
$token = filter_input(INPUT_POST, 'g-recaptcha-response', FILTER_SANITIZE_STRING);
$type = ($_POST['type'] ?? '' == 'v2') ? 'v2' : 'v3';
$hash = filter_input(INPUT_POST, 'hash', FILTER_SANITIZE_STRING);

if (strlen($token) > 35) {
	$robot = new Utils\ReCaptcha($token, $type);
    // Verifica o tipo da verificação robótica para validá-la
	if ($type == 'v3') {
		$isRobot = $robot->isOrNotV3();
	} else {
		$isRobot = $robot->isOrNotV2();
	}
}

// Se passou na verificação robótica
if (!$isRobot) {
    // Se os cookies estão ativados e o identificador da sessão está salvo neles
	if (!empty($session_id)) {
		$db = new Utils\Database('matches');
        // Se já foi escolhida uma hash (determinada pelo sistema na partida anterior)
		if (!empty($hash) && Utils\Hash::validateHash($hash)) {
			$game = $db->select(['col' => 'hash', 'val' => $hash])->fetch();
            // Se ainda não existir uma partida com essa hash
			if (empty($game)) {
				$key = $hash;
			} else {
				header('Location: play?person=nobot&room_key=' . $hash);
				die;
			}
		} else {
			$key = Utils\Hash::generateHash();
		}
        // Gera a hash da próxima partida
		$next = Utils\Hash::generateHash();
		$db->insert(['hash' => $key, 'x' => $session_id, 'next' => $next]);
		header('Location: play?room_key=' . $key);
	}
}
