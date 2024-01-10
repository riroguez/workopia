<?php
namespace Framework; 

use PDO;
use Exception;
use PDOException;

class Database
{
     public $conn;

    /**
     * Contructor for Database class
     * 
     * @param array $config
     */

     public function __construct($config)
     {
          $dns = "{$config['type']}:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";

          $options = [
               PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
               PDO::ATTR_EMULATE_PREPARES => FALSE,
               PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
          ];

          try {
               $this->conn = new PDO($dns, $config['user'], $config['pass'], $options);
          } catch (PDOException $e) {
               echo "Database connection failed: " . $e->getMessage();
               exit;
          }
     }

     /**
      * Query the database
      * 
      * @param string $query
      * 
      * @return PDOStatament
      * @throws PDOException
      */
      public function query($query, $params = [])
      {
          try {
               $sth = $this->conn->prepare($query);
               foreach($params as $param => $value) {
                    
                    $sth->bindValue(':' . $param, $value);
               }
               $sth->execute();
               return $sth;
          } catch (PDOException $e) {
               throw new Exception("Query failed to execute: {$e->getMessage()}");
          }
      }

}#end class