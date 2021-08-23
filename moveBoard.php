<?php
require 'vendor/autoload.php';

session_name('TicTacToe');
session_start();

$session_id = session_id()??null;

use Leone\Game\TicTacToe\Utils;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$room_key = filter_input(INPUT_POST, 'room_key', FILTER_SANITIZE_STRING);

$casa = intval($_POST['position']);
$be = ($_POST['per']==='true');

$valid = Utils\Hash::validateHash($room_key);

$result['success'] = false;

try {
  if (!$valid) {
    throw new Exception('Código inválido!');
  }
  
  $db = new Utils\Database('matches');
  $game = $db->select(['col' => 'hash', 'val' => $room_key])->fetch();
  
  if (empty($game) || empty($session_id)) {
    throw new Exception('Código inválido!');
  }
  
  if ($be) {
    if ($game['x'] != $session_id) {
      throw new Exception('Código inválido!');
    }
  }else{
    if ($game['o'] != $session_id) {
      throw new Exception('Código inválido!');
    }
  }
  
  if ($casa > 9 || $casa < 1) {
    throw new Exception('Casa inválida!');
  }
  
  if ($game[$casa]!==null) {
    throw new Exception('Casa já ocupada!');
  }
  
  $db->update(['matches.'.$casa => intval($be)], 'hash = "'.$room_key.'"');
  $result['success'] = true;
  $result['code'] = 200;
} catch (Exception $e) {
  $code = $e->getCode();
  $result['code'] = ($code===0)?400:$code;
  $result['message'] = $e->getMessage();
} finally{
  echo json_encode($result);
}