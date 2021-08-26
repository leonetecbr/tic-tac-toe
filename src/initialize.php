<?php
use Leone\Game\TicTacToe\Utils;

$isRobot = true;
$token = filter_input(INPUT_GET, 'g-recaptcha-response', FILTER_SANITIZE_STRING);
$type = ($_GET['type']??''=='v2')?'v2':'v3';
$hash = filter_input(INPUT_GET, 'hash', FILTER_SANITIZE_STRING);
if (strlen($token)>35) {
  $robot = new Utils\ReCaptcha($token, $type);
  if ($type=='v3') {
    $isRobot = $robot->isOrNotV3();
  }else {
    $isRobot = $robot->isOrNotV2();
  }
}

if (!$isRobot) {
  if (!empty($session_id)) {
    $db = new Utils\Database('matches');
    if (!empty($hash) && Utils\Hash::validateHash($hash)) {
      $game = $db->select(['col' => 'hash', 'val' => $hash])->fetch();
      if (empty($game)) {
        $key = $hash;
      }else {
        header('Location: play?person=nobot&room_key='.$hash);
        die;
      }
    }else{
      $key = Utils\Hash::generateHash();
    }
    $next = Utils\Hash::generateHash();
    $db->insert(['hash' => $key, 'x' => $session_id, 'next' => $next]);
    header('Location: play?room_key='.$key);
  }
}