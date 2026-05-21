<?php
require_once(__DIR__ . '/../Services/EmailService.php');
require_once (__DIR__ . "/../Views/include/popup.php");

class ContactoC {

    public function mostrarFormulario() {
        include(__DIR__ . '/../Views/Usuario/contacto.php');
    }

    public function enviarMensajeContacto() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $nombre   = trim($_POST['nombre']);
            $apellido = trim($_POST['apellido']);
            $correo   = trim($_POST['correo']);
            $mensaje  = trim($_POST['mensaje']);

            if (empty($nombre) || empty($apellido) || empty($correo) || empty($mensaje)) {
                $_SESSION['tipo_mensaje'] = "warning";
                $_SESSION['mensaje'] = "⚠️ Por favor completa todos los campos antes de enviar el formulario.";
                header("Location: Index.php?accion=contactanos");
                exit();
            }

            $emailService = new EmailService();

            // Construir cuerpo del mensaje en formato HTML
            $contenido = "
                <strong>Nombre:</strong> $nombre<br>
                <strong>Apellido:</strong> $apellido<br>
                <strong>Correo o celular:</strong> $correo<br><br>
                <strong>Mensaje:</strong><br>
                <p>$mensaje</p>
            ";

            // Enviar correo al administrador
            $destinatario = 'tecnicosyasociados0@gmail.com';
            $asunto = isset($_SESSION['usuario'])
                ? "Nuevo mensaje de contacto de {$_SESSION['usuario']} ($nombre $apellido)"
                : "Nuevo mensaje de contacto de $nombre $apellido";

            $enviado = $emailService->enviarNotificacion($destinatario, $asunto, $contenido);

            if ($enviado) {
                $_SESSION['tipo_mensaje'] = "success";
                $_SESSION['mensaje'] = "Tu mensaje ha sido enviado correctamente. ¡Gracias por contactarnos!";
            } else {
                $_SESSION['tipo_mensaje'] = "error";
                $_SESSION['mensaje'] = "Hubo un error al enviar tu mensaje. Por favor intenta nuevamente más tarde.";
            }

            // Redirección limpia (refresca la página)
            header("Location: Index.php?accion=contactanos");
            exit();
        }
    }
}