<?php
require 'config.php';

if ($conn) {
    echo "Conexión exitosa a la base de datos.";
} else {
    echo "No se pudo conectar a la base de datos.";
}
?>
