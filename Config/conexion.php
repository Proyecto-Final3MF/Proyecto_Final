<?php
function conectar() {
    $host = 'localhost';
    $usuario = 'root';
    $clave = '';
    $base_datos = 'tecnicosasociados';

    $conexion = new mysqli($host, $usuario, $clave, $base_datos);

    if ($conexion->connect_error) {
        die('Error de conexión: ' . $conexion->connect_error);
    }

    return $conexion;
}