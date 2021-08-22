<?php
use Leone\Game\TicTacToe\Utils;
use Dotenv\Dotenv;

if (!empty($session_id)) {
  $key = Utils\Hash::generateHash();
  $dotenv = Dotenv::createImmutable(__DIR__.'/..');
  $dotenv->load();
  $db = new Utils\Database('matches');
  $db->insert(['hash' => $key, 'x' => $session_id]);
  header('Location: ?room_key='.$key);
}