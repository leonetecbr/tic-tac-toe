<?php

use TicTacToe\Utils;

$room_key = filter_input(INPUT_GET, 'room_key', FILTER_SANITIZE_STRING);

$valid = Utils\Hash::validateHash($room_key);

// Se a chave for valida
if ($valid) {
	$db = new Utils\Database('matches');
	$game = $db->select(['col' => 'hash', 'val' => $room_key])->fetch();

	if (!empty($game) && !empty($session_id)) {
		if ($game['x'] == $session_id) {
			$be = 'true';
			$person = true;
		} elseif ((empty($game['o']) || $game['o'] == $session_id) && $person) {
			$be = 'false';
			$db->update(['o' => $session_id], 'hash = "' . $room_key . '"');
		} else {
			$valid = false;
		}
	} else {
		$valid = false;
	}
}
