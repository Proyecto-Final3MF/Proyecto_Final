<?php
session_start();
require_once(__DIR__ . '/NotificacionC.php');

$controller = new NotificacionC();
$controller->marcarTodasLeidas('urgente');  // Solo urgentes

// Devuelve solo JSON, sin HTML ni scripts
header('Content-Type: application/json');
echo json_encode(['success' => true]);
exit();