<?php
class Database {
    private static $instance = null;
    private $pdo;
    
    private function __construct() {
        $servername = '127.0.0.1';
        $database = 'bd_mundo';
        $username = 'root';
        $password = '123mudar';
        
        try {
            $this->pdo = new PDO(
                "mysql:host=$servername;dbname=$database;charset=utf8mb4",
                $username,
                $password
            );
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Erro na conexão: " . $e->getMessage());
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->pdo;
    }
    
    public function prepare($sql) {
        return $this->pdo->prepare($sql);
    }
    
    public function query($sql) {
        return $this->pdo->query($sql);
    }
    
    public function lastInsertId() {
        return $this->pdo->lastInsertId();
    }
}
?>
