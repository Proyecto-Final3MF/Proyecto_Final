<?php

require_once __DIR__ . '/../Models/solicitud_historiaM.php';

class HistoriaC {
    private $solicitudHistoria;

    public function __construct() {
        $this->solicitudHistoria = new HistoriaM();
    }

    public function registrarEvento($id_solicitud, $evento) {
        $this->solicitudHistoria->registrarEvento($id_solicitud, $evento);
    }

    public function mostrarHistoria() {
        $id_solicitud = $_GET['id_solicitud'];

        $historia = new HistoriaM();
        $resultados = $historia->mostrarHistoria($id_solicitud);
        include(__DIR__ . "/../Views/Solicitudes/Historia.php");
    }
}
