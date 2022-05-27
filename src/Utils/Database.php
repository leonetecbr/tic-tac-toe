<?php

namespace TicTacToe\Utils;

use PDO;
use PDOException;

/**
 * Cria e gerencia a conexão com o banco de dados
 */
class Database
{
    /**
     * Nome da tabela
     * @var mixed|null
     */
    private $table;

    /**
     * Conexão com o bando de dados
     * @var PDO
     */
    private $connection;

    /**
     * Define o nome da tabela e cria a conexão da conexão
     */
    public function __construct($table = null)
    {
        $this->table = $table;
        $this->setConnection();
    }

    /**
     * Cria uma conexão com o banco de dados
     * @return void
     */
    private function setConnection(): void
    {
        try {
            $this->connection = new PDO('mysql:host=' . $_ENV['HOST_DB'] . ';dbname=' . $_ENV['BANK_DB'], $_ENV['USER_DB'], $_ENV['PASS_DB']);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            http_response_code(500);
            die;
        }
    }

    /**
     * Executa queries dentro do banco de dados
     * @param string $query
     * @param array $params
     * @return mixed
     */
    public function execute(string $query, array $params = [])
    {
        try {
            $statement = $this->connection->prepare($query);
            $statement->execute($params);
            return $statement;
        } catch (PDOException $e) {
            http_response_code(500);
            die;
        }
    }

    /**
     * Insere registros no banco de dados
     * @param array $values
     * @return PDO
     */
    public function insert(array $values): PDO
    {
        $fields = array_keys($values);
        $binds  = array_pad([], count($fields), '?');
        $query = 'INSERT INTO ' . $this->table . ' (' . implode(',', $fields) . ') VALUES (' . implode(',', $binds) . ')';
        $this->execute($query, array_values($values));
        return $this->connection;
    }

    /**
     * Faz consultas no banco de dados
     * @param array $where
     * @param int|null $limit
     * @param string $fields
     * @param string|null $order
     * @return mixed
     */
    public function select(array $where = [], int $limit = null, string $fields = '*', string $order = null)
    {
        if (!empty($where)) {
            $text = ' WHERE ' . $where['col'] . ' = ?';
            $params[0] = $where['val'];
        } else {
            $text = '';
            $params = [];
        }
        $order = strlen($order) ? ' ORDER BY ' . $order : '';
        $limit = strlen($limit) ? ' LIMIT ' . $limit : '';
        $query = 'SELECT ' . $fields . ' FROM ' . $this->table . $text . $limit . $order;
        return $this->execute($query, $params);
    }

    /**
     * Atualiza registros no banco de dados
     * @param array $values
     * @param string $where
     * @return bool
     */
    public function update(array $values, string $where): bool
    {
        $fields = array_keys($values);
        $query = 'UPDATE ' . $this->table . ' SET ' . implode('=?,', $fields) . '=? WHERE ' . $where;
        return $this->execute($query, array_values($values));
    }

    /**
     * Deleta registros no banco de dados
     * @param array $where
     * @return bool
     */
    public function delete(array $where): bool
    {
        $text = "WHERE {$where['col']} = ?";
        $params[0] = $where['val'];
        $query = "DELETE FROM $this->table $text";
        return $this->execute($query, $params);
    }

    /**
     * Altera a tabela padrão para queries no dados do banco
     * @param string $table
     * @return void
     */
    public function setTable($table): void
    {
        if ($table !== $this->table) {
            $this->table = $table;
        }
    }
}
