<?php
class PublicC {

    public function inicio() {
        include(__DIR__ . "/../Views/Usuario/Inicio.php");
    }

    public function nosotros() {
        include(__DIR__ . "/../Views/Usuario/Nosotros.php");
    }

    public function contacto() {
        include(__DIR__ . "/../Views/Usuario/Contacto.php");
    }

    public function terminos() {
        include(__DIR__ . "/../Views/Usuario/Terminos.php");
    }
}
