<?php
use Leone\Game\TicTacToe\Utils\Hash;

session_name('TicTacToe');
session_start();

$session_id = session_id()??null;

if (!empty($session_id)) {
  $key = Hash::generateHash();
  header('Location: ?room_key='.$key);
}