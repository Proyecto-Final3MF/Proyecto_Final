<?php
class PublicC {

    public function inicio() {
        include(__DIR__ . "/../Views/Usuario/Inicio.php");
    }

    public function nosotros() {
        include(__DIR__ . "/../Views/Usuario/nosotros.php");
    }

    public function contacto() {
        include(__DIR__ . "/../Views/Usuario/contacto.php");
    }

    public function terminos() {
        include(__DIR__ . "/../Views/Usuario/terminos.php");
    }
}
