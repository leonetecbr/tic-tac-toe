<?php
date_default_timezone_set('America/Bahia');

function checkEnd(array $game){
  $date = new DateTime($game['created']);
  $timestamp = $date->getTimestamp();
  
  if ((time()-$timestamp)>600) {
    return true;
  }
  
  $marked = 0;
  for ($i=1;$i<10; $i++) {
    if ($game[$i]!==null) {
      $marked++;
    }
  }
  
  if ($marked==9) {
    return true;
  } elseif (($game[1]=='1' && $game[2]=='1' && $game[3]=='1') || ($game[4]=='1' && $game[5]=='1' && $game[6]=='1') || ($game[7]=='1' && $game[8]=='1' && $game[9]=='1')) {
    return true;
  } elseif (($game[1]=='0' && $game[2]=='0' && $game[3]=='0') || ($game[4]=='0' && $game[5]=='0' && $game[6]=='0') || ($game[7]=='0' && $game[8]=='0' && $game[9]=='0')) {
    return true;
  } elseif (($game[1]=='1' && $game[4]=='1' && $game[7]=='1') || ($game[2]=='1' && $game[5]=='1' && $game[8]=='1') || ($game[3]=='1' && $game[6]=='1' && $game[9]=='1')) {
    return true;
  } elseif (($game[1]=='0' && $game[4]=='0' && $game[7]=='0') || ($game[2]=='0' && $game[5]=='0' && $game[8]=='0') || ($game[3]=='0' && $game[6]=='0' && $game[9]=='0')) {
    return true;
  } elseif (($game[1]=='1' && $game[5]=='1' && $game[9]=='1') || ($game[3]=='1' && $game[5]=='1' && $game[7]=='1')) {
    return true;
  } elseif (($game[1]=='0' && $game[5]=='0' && $game[9]=='0') || ($game[3]=='0' && $game[5]=='0' && $game[7]=='0')) {
    return true;
  }else{
    return false;
  }
}