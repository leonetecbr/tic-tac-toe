<?php 

namespace Leone\Game\TicTacToe\Utils;

/**
 * 
 */
class Hash{
  
  /**
   * 
   */
  public static function generateHash(int $len = 10){
    $hash = md5(time());
    $chars = 'ABCDEFabcdef0123456789';
    for($i=0;$i<$len;$i++){
     $hash .= $chars[mt_rand(0,21)];
    }
    return $hash;
  }
  
  /**
   * 
   */
  public static function validateHash(string $hash, int $len = 42){
    return (ctype_xdigit($hash) && strlen($hash)==$len);
  }
}