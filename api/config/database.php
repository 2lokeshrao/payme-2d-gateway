<?php
/**
 * Database Configuration
 * PostgreSQL Connection with PDO
 */

class Database {
    private $host = 'localhost';
    private $db_name = 'payme_gateway';
    private $username;
    private $password;
    private $conn;

    public function __construct() {
        // Get credentials from environment variables
        $this->username = getenv('PGUSER') ?: 'postgres';
        $this->password = getenv('PGPASSWORD') ?: '';
    }

    /**
     * Get database connection
     * @return PDO|null
     */
    public function getConnection() {
        $this->conn = null;

        try {
            $dsn = "pgsql:host=" . $this->host . ";dbname=" . $this->db_name;
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        } catch(PDOException $e) {
            error_log("Connection Error: " . $e->getMessage());
            return null;
        }

        return $this->conn;
    }

    /**
     * Close database connection
     */
    public function closeConnection() {
        $this->conn = null;
    }
}
?>
