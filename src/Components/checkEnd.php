<?php
date_default_timezone_set('America/Bahia');

function checkEnd(array $game): bool
{
	$date = new DateTime($game['created']);
	$timestamp = $date->getTimestamp();

	if ((time() - $timestamp) > 600) {
		return true;
	}

	$marked = 0;
	for ($i = 1; $i < 10; $i++) {
		if ($game[$i] !== null) {
			$marked++;
		}
	}

	if ($marked == 9) {
		return true;
	} elseif (!is_null($game[3]) && (($game[1] === $game[3] && $game[2] === $game[3]) || ($game[6] === $game[3] && $game[9] === $game[3]))) {
        return true;
    } else if (!is_null($game[7]) && (($game[1] === $game[7] && $game[4] === $game[7]) || ($game[8] === $game[7] && $game[9] === $game[7]))){
        return true;
    } else if (!is_null($game[5]) && (($game[1] === $game[5] && $game[9] === $game[5]) || ($game[3] === $game[5] && $game[7] === $game[5]) || ($game[2] === $game[5] && $game[8] === $game[5]) || ($game[4] === $game[5] && $game[6] === $game[5]))){
        return true;
    } else {
        return false;
	}
}
