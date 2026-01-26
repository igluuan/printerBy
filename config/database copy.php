<?php
class Database {
    private static $instance = null;
    private $conn;
    private $host;
    private $db_name;
    private $username;
    private $password;

    private function __construct() {
        // Carregar variáveis de ambiente ou usar padrões
        $this->host = "";
        $this->db_name = "";
        $this->username = "";
        $this->password = "";
        
        $this->connect();
    }

    /**
     * Obter instância única do banco de dados (Singleton)
     * Reutiliza a mesma conexão em todas as requisições
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance->getConnection();
    }

    /**
     * Conectar ao banco de dados
     */
    private function connect() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                'mysql:host=' . $this->host . ';dbname=' . $this->db_name . ';charset=utf8mb4',
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_PERSISTENT => false,
                    PDO::ATTR_TIMEOUT => 5
                ]
            );
            
            // Garantir que o MySQL também usa o timezone correto
            try {
                $this->conn->exec("SET time_zone = '-03:00'");
            } catch(Exception $e) {
                // Ignorar erros de timezone
            }
        } catch(PDOException $e) {
            // Armazenar o erro mas não lançar exceção aqui
            // Deixar que getInstance() handle isso
            throw $e;
        }
    }

    /**
     * Obter conexão (compatibilidade com código antigo)
     */
    public function getConnection() {
        return $this->conn;
    }

    /**
     * Método compatível com código legado
     */
    public function connect_legacy() {
        return $this->conn;
    }
}
?>
