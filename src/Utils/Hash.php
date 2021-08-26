<?php 
namespace Leone\Game\TicTacToe\Utils;

/**
 * Valida e gera as hashes que são as chaves das partidas
 */
class Hash{
  
  /**
   * Gera a chave para a partida tamanho do acréscimo padrão é 10, totalizando 42 caracteres
   * #return string
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
   * Faz a validação da chave da sala que são strinsgs de 42 caracteres alfanuméricos
   * @ return boolean
   */
  public static function validateHash(string $hash, int $len = 42){
    return (ctype_xdigit($hash) && strlen($hash)==$len);
  }
}