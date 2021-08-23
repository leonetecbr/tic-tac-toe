<?php
namespace Leone\Game\TicTacToe\Utils;

use PDO;
use PDOException;

/**
 * Classe responsável por fazer a conexão com o banco de dados
 */
class Database{
  private $table;
  private $connection;
  
  /**
   * Define a tabela e a instância da conexão
   */
  public function __construct($table=null){
    $this->table = $table;
    $this->setConnection();
  }
  
  /**
   * Método responsável por criar uma conexão com o banco de dados
   */
  private function setConnection(){
    try{
      $this->connection = new PDO('mysql:host='.$_ENV['HOST_DB'].';dbname='.$_ENV['BANK_DB'],$_ENV['USER_DB'],$_ENV['PASS_DB']);
      $this->connection->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    }catch(PDOException $e){
      $content = "\n\n".$e->getMessage();
      file_put_contents('logs/error_db.txt', $content, FILE_APPEND);
      die('500');
    }
  }
  
  /**
   * Método responsável por executar queries dentro do banco de dados
   * @return PDOStatement
   */
  public function execute(string $query, array $params = []){
    try{
      $statement = $this->connection->prepare($query);
      $statement->execute($params);
      return $statement;
    }catch(PDOException $e){
      $content = "\n\n".$e->getMessage();
      file_put_contents('logs/error_db.txt', $content, FILE_APPEND);
      die;
    }
  }

  /**
   * Método responsável por inserir dados no banco
   * @return integer ID inserido
   */
  public function insert(array $values){
    $fields = array_keys($values);
    $binds  = array_pad([],count($fields),'?');
    $query = 'INSERT INTO '.$this->table.' ('.implode(',',$fields).') VALUES ('.implode(',',$binds).')';
    $this->execute($query,array_values($values));
    return $this->connection;
  }

  /**
   * Método responsável por executar uma consulta no banco
   * @return PDOStatement
   */
  public function select(array $where = [], int $limit = null, string $fields = '*', string $order = null){
    if (!empty($where)) {
      $text = ' WHERE '.$where['col'].' = ?';
      $params[0] = $where['val'];
    }else{
      $text = '';
      $params = [];
    }
    $order = strlen($order)?' ORDER BY '.$order:'';
    $limit = strlen($limit)?' LIMIT '.$limit : '';
    $query = 'SELECT '.$fields.' FROM '.$this->table.$text.$limit.$order;
    return $this->execute($query, $params);
  }

  /**
   * Método responsável por executar atualizações no banco de dados
   * @return boolean
   */
  public function update(array $values, string $where){
    $fields = array_keys($values);
    $query = 'UPDATE '.$this->table.' SET '.implode('=?,', $fields).'=? WHERE '.$where;
    $this->execute($query, array_values($values));
    return true;
  }

  /**
   * Método responsável por excluir dados do banco
   */
  public function delete(array $where){
    $text = 'WHERE '.$where['col'].' = ?';
    $params[0] = $where['val'];
    $query = 'DELETE FROM '.$this->table.' '.$text;
    $this->execute($query, $params);
    return true;
  }
  
  /**
   * Método responsável por alterar a tabela padrão para querys no dados do banco
   * @param string $table
   */
  public function setTable($table){
    if ($table !== $this->table){
      $this->table = $table;
    }
  }

}