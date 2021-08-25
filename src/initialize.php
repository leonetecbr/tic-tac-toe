<?php
use Leone\Game\TicTacToe\Utils;

$isRobot = true;
$token = filter_input(INPUT_GET, 'g-recaptcha-response', FILTER_SANITIZE_STRING);
$type = ($_GET['type']??''=='v2')?'v2':'v3';
if (strlen($token)>35) {
  $robot = new Utils\ReCaptcha($token, $type);
  if ($type=='v3') {
    $isRobot = $robot->isOrNotV3();
  }else {
    $isRobot = $robot->isOrNotV2();
  }
}

if (!empty($session_id) && !$isRobot) {
  $key = Utils\Hash::generateHash();
  $db = new Utils\Database('matches');
  $db->insert(['hash' => $key, 'x' => $session_id]);
  header('Location: ?room_key='.$key);
}