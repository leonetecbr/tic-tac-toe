<?php
date_default_timezone_set('America/Bahia');

/**
 * Verifica se o jogo deve ser finalizado
 * @param array $game
 * @return bool
 * @throws Exception
 */
function checkEnd(array $game): bool
{
    $date = new DateTime($game['created']);
    $timestamp = $date->getTimestamp();

    // Se já tiver passado 10 minutos do início
	if ((time() - $timestamp) > 600) {
		return true;
	}

    // Conta as casas marcadas
	$marked = 0;
	for ($i = 1; $i < 10; $i++) {
		if ($game[$i] !== null) {
			$marked++;
		}
	}

    // Se todas as casas já foram marcadas
	if ($marked == 9) {
		return true;
	}
    // Se alguém venceu
    elseif (!is_null($game[3]) && (($game[1] === $game[3] && $game[2] === $game[3]) || ($game[6] === $game[3] && $game[9] === $game[3]))) {
        return true;
    } else if (!is_null($game[7]) && (($game[1] === $game[7] && $game[4] === $game[7]) || ($game[8] === $game[7] && $game[9] === $game[7]))){
        return true;
    } else if (!is_null($game[5]) && (($game[1] === $game[5] && $game[9] === $game[5]) || ($game[3] === $game[5] && $game[7] === $game[5]) || ($game[2] === $game[5] && $game[8] === $game[5]) || ($game[4] === $game[5] && $game[6] === $game[5]))){
        return true;
    }
    // Se a partida continua
    else {
        return false;
	}
}
