<?php
require 'vendor/autoload.php';
require 'src/checkEnd.php';

session_name('TicTacToe');
session_start();

$session_id = session_id()??null;

use Leone\Game\TicTacToe\Utils;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$room_key = filter_input(INPUT_GET, 'room_key', FILTER_SANITIZE_STRING);

$valid = Utils\Hash::validateHash($room_key);

$result['success'] = false;
$result['connect'] = false;

try {
  if (!$valid) {
    throw new Exception('Código inválido!');
  }
  
  $db = new Utils\Database('matches');
  $game = $db->select(['col' => 'hash', 'val' => $room_key])->fetch();
  
  if (empty($game) || empty($session_id)) {
    throw new Exception('Código inválido!');
  }
  
  if ($game['x'] != $session_id && $game['o'] != $session_id) {
    throw new Exception('Código inválido!');
  }
  
  $ended = checkEnd($game);
  
  if ($ended) {
    $db->delete(['col' => 'hash', 'val' => $room_key]);
  }
  
  $result['dados']['vez'] = true;
  for ($i=1;$i<10; $i++) {
    $result['dados'][$i] = $game[$i];
    if($game[$i]!==null){      $result['dados']['vez'] = !$result['dados']['vez'];
    }
  }
  
  if (!empty($game['o'])) {
    $result['connect'] = true;
  }
  
  $result['success'] = true;
  $result['code'] = 200;
} catch (Exception $e) {
  $code = $e->getCode();
  $result['code'] = ($code===0)?400:$code;
  $result['message'] = $e->getMessage();
} finally{
  echo json_encode($result);
}