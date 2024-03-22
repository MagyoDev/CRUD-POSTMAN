<?php
namespace App\Database;
use PDO;
use PDOException;
use MongoDB\Client as MongoClient;
use Exception;

class Database {
    private static $instance = null;
    private $conn;
    private $config;

    private function __construct() {
        // Carregar as configurações do arquivo config.php
        $this->config = require 'config.php';
        $dbConfig = $this->config['database'];
        $driver = $dbConfig['driver'];

        try {
            switch ($driver) {
                case 'mysql':
                    // Configurações específicas para MySQL
                    $mysqlConfig = $dbConfig['mysql'];
                    $dsn = "mysql:host={$mysqlConfig['host']};dbname={$mysqlConfig['db_name']};charset={$mysqlConfig['charset']}";
                    $this->conn = new PDO($dsn, $mysqlConfig['username'], $mysqlConfig['password'], [PDO::ATTR_PERSISTENT => true]);
                    break;
                case 'sqlite':
                    // Configurações específicas para SQLite
                    $sqliteConfig = $dbConfig['sqlite'];
                    $dsn = "sqlite:{$sqliteConfig['path']}";
                    $this->conn = new PDO($dsn, null, null, [PDO::ATTR_PERSISTENT => true]);
                    break;
                case 'sqlsrv':
                    // Configurações específicas para Sqlsrv
                    $sqlsrvConfig = $dbConfig['sqlsrv'];
                    $dsn = "sqlsrv:Server={$sqlsrvConfig['host']};Database={$sqlsrvConfig['db_name']}";
                    $this->conn = new PDO($dsn, $sqlsrvConfig['username'], $sqlsrvConfig['password'], [PDO::ATTR_PERSISTENT => true]);
                    break;
                case 'pgsql':
                    // Configurações específicas para pgsql
                    $pgsqlConfig = $dbConfig['pgsql'];
                    $dsn = "pgsql:host={$pgsqlConfig['host']};port={$pgsqlConfig['port']};dbname={$pgsqlConfig['db_name']};user={$pgsqlConfig['username']};password={$pgsqlConfig['password']}";
                    $this->conn = new PDO($dsn);
                    break;
            }

            if (in_array($driver, ['mysql', 'sqlite', 'sqlsrv', 'pgsql'])) {
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
        } catch(PDOException $exception) {
            echo "Erro de conexão: " . $exception->getMessage();
        } catch(Exception $exception) {
            echo "Erro de conexão (MongoDB): " . $exception->getMessage();
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance->conn;
    }

    private function __clone() {}
}