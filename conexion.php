<?php
// Configuración de la base de datos
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'almacen');

class Database {
    private $connection;

    // Constructor para establecer la conexión
    public function __construct() {
        $this->connect();
    }

    // Método para conectar a la base de datos
    private function connect() {
        $this->connection = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

        // Verificar la conexión
        if ($this->connection->connect_error) {
            $this->handleError($this->connection->connect_error);
        }
    }

    // Método para manejar errores
    private function handleError($error) {
        error_log($error); // Registrar el error en un archivo de registro
        die("Error de conexión a la base de datos. Por favor, intenta más tarde.");
    }

    // Método para obtener la conexión
    public function getConnection() {
        return $this->connection;
    }

    // Destructor para cerrar la conexión
    public function __destruct() {
        if ($this->connection) {
            $this->connection->close();
        }
    }
}

// Crear una instancia de la clase Database
$db = new Database();
$conn = $db->getConnection();
?>
