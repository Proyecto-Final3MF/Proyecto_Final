<?php
require_once(__DIR__ . "/../Views/include/popup.php");
require_once(__DIR__ . '/../Models/SolicitudM.php');
require_once(__DIR__ . '/../Models/ReviewM.php');

class ReviewC {
    private $ReviewModel;
    private $HistorialModel;
    private $historiaC;

    public function __construct() {
        $this->ReviewModel = new Review();
        $this->solicitudModel = new Solicitud();
        $this->HistorialModel = new HistorialController();
        $this->historiaC = new HistoriaC();
    }

    public function FormularioR() {
        $id = $_GET['id_solicitud'] ?? null;

        $rating = 0;
        $Comentario = '';

        if (!$id) {
            $_SESSION['tipo_mensaje'] = "warning";
            $_SESSION['mensaje'] = "Error: ID de solicitud no proporcionado.";
            header("Location: Index.php?accion=listarST");
            exit();
        }

        //Busca si la solicitud ya tiene calificacion
        $YaExiste = $this->ReviewModel->YaCalificado($id);
        if ($YaExiste) {
            $rating = ($YaExiste['rating'] ?? 0) * 2; //Pasa de 0.5 a 5 para 1 a 10.
            $Comentario = $YaExiste['comentario'] ?? '';
            $id_solicitud = $YaExiste['id_solicitud'] ?? $id;
        }

        $datosSolicitud = $this->solicitudModel->obtenerSolicitudPorId($id);

        if (!$datosSolicitud) {
            $_SESSION['tipo_mensaje'] = "warning";
            $_SESSION['mensaje'] = "Error: Solicitud no encontrada.";
            header("Location: Index.php?accion=listarST");
            exit();
        }

        $titulo_solicitud = $datosSolicitud['titulo'];
        $id_tecnico = $datosSolicitud['tecnico_id'];

        include(__DIR__ . "/../Views/Solicitudes/review.php");
    }

    public function AddReview() {
        $rating = $_POST['rating'] ?? 0;
        $rating = $rating / 2; //Pasa de 1 a 10 para 0.5 a 5
        $Comentario = trim($_POST['Comentario']) ?? ''; //Saca espacio al inicio y final del comentario
        $id_tecnico = $_POST['id_tecnico'];
        $id_cliente = $_SESSION['id'];
        $id_solicitud = $_POST['id_solicitud'] ?? null;
        $titulo_solicitud = $_POST['titulo_solicitud'] ?? '';

        $checkU = $this->solicitudModel->checkUsuario($id_solicitud, $id_tecnico, $id_cliente);
        if (!$checkU) {
            $_SESSION['tipo_mensaje'] = "error";
            $_SESSION['mensaje'] = "Accesso negado";
            header("Location:Index.php?accion=listarST");
            exit();
        }

        if ($rating == 0) {
            $_SESSION['tipo_mensaje'] = "warning";
            $_SESSION['mensaje'] = "El valor minimo es media estrella";
            header("Location:Index.php?accion=FormularioReview&id_solicitud=" . $id_solicitud);
            exit();
        }

        if (empty($titulo_solicitud) || $titulo_solicitud === '') {
            $_SESSION['tipo_mensaje'] = "error";
            $_SESSION['mensaje'] = "No se puede evaluar en este momento.";
            header("Location:Index.php?accion=FormularioReview&id_solicitud=" . $id_solicitud);
            exit();
        }

        //Agarra la cantidad de reviews q tiene el tecnico
        $HayReview = $this->ReviewModel->obtenerCantReview($id_tecnico);
        if ($HayReview === null) {
            $_SESSION['tipo_mensaje'] = "error";
            $_SESSION['mensaje'] = "No se puede evaluar en este momento.";
            header("Location:Index.php?accion=FormularioReview&id_solicitud=" . $id_solicitud);
            exit();
        }

        //Agarra el promedio del tecnico
        $HayPromedio = $this->ReviewModel->obtenerPromedio($id_tecnico);
        if ($HayPromedio === null) {
            $_SESSION['tipo_mensaje'] = "error";
            $_SESSION['mensaje'] = "No se puede evaluar en este momento.";
            header("Location:Index.php?accion=FormularioReview&id_solicitud=" . $id_solicitud);
            exit();
        }

        //Agarra la suma de todas las calificacciones del tecnico
        $HaySuma = $this->ReviewModel->obtenerSuma($id_tecnico);
        if ($HaySuma === null) {
            $_SESSION['tipo_mensaje'] = "error";
            $_SESSION['mensaje'] = "No se puede evaluar en este momento.";
            header("Location:Index.php?accion=FormularioReview&id_solicitud=" . $id_solicitud);
            exit();
        }

        $suma_rating = $HaySuma;
        $ratingPromedio = $HayPromedio;
        $CantReview = $HayReview;

        //Si la review ya fue hecha en lugar de crear el cliente va editarla.
        $YaExiste = $this->ReviewModel->YaCalificado($id_solicitud);
        if ($YaExiste) {
            $ratingAntiguo = $YaExiste['rating'];
            $ComentarioAntiguo = $YaExiste['comentario'];

            //Calculos
            $suma_rating = ((($suma_rating) - $ratingAntiguo) + $rating);
            $ratingPromedio =  $suma_rating / $CantReview;
            $ratingPromedio = round($ratingPromedio * 2) / 2;

            $this->ReviewModel->updateReview($suma_rating, $ratingPromedio, $rating, $Comentario, $id_solicitud, $id_tecnico, $CantReview);
            $_SESSION['tipo_mensaje'] = "success";
            $_SESSION['mensaje'] = "Gracias por compartir tu experiencia.";

            //Detecta q cambio para registrar en el historial
            if ($Comentario == $ComentarioAntiguo && $ratingAntiguo == $rating) {
                $obs = "Ningun cambio detectado";
            } else {
                if ($ratingAntiguo !== $rating) {
                    $obs1 = "Rating: " . $ratingAntiguo . "⭐" . " ⟶ " . $rating . "⭐" . " ‎ ";
                    $obs = $obs1;
                }

                if ($Comentario !== $ComentarioAntiguo) {
                    if ($ComentarioAntiguo == ''): $ComentarioAntiguo = "' '";
                    endif;
                    if ($Comentario == ''): $ComentarioAntiguo = "' '";
                    endif;
                    $obs2 = "Comentario: " . "'" . $ComentarioAntiguo . "'" . " ⟶ " . "'" . $Comentario . "'";
                    $obs = $obs2;
                }
                $obs = $obs1 . $obs2;
            }
            $evento = "La calificación fue cambiada para " . $rating . "⭐";
            $this->historiaC->registrarEvento($id_solicitud, $evento);
            $this->HistorialModel->registrarModificacion($_SESSION['usuario'], $_SESSION['id'], "editó su evaluación de la solicitud", $titulo_solicitud, $id_solicitud, $obs);
            require_once(__DIR__ . '/../Controllers/NotificacionC.php');
            $notificacion = new NotificacionC();
            $notificacion->crearNotificacion($id_tecnico, "Tu calificación en la solicitud '$titulo_solicitud' fue editada.", 'urgente');

            header("Location:Index.php?accion=listarST");
            exit();
        }

        //Calcula todos los valores para la base de datos
        $suma_rating = $suma_rating + $rating;
        $ratingPromedio = $suma_rating / ($CantReview + 1);
        $CantReview += 1;

        $ratingPromedio = round($ratingPromedio * 2) / 2;
        $ratingPromedio = max(0.5, min(5, $ratingPromedio));
        $this->ReviewModel->AddReview($suma_rating, $CantReview, $ratingPromedio, $rating, $id_tecnico, $id_cliente, $Comentario, $id_solicitud);

        //Cosas del historial
        $evento = "La Solicitud fue calificada con " . $rating . "⭐";
        $this->historiaC->registrarEvento($id_solicitud, $evento);
        $this->HistorialModel->registrarModificacion($_SESSION['usuario'], $_SESSION['id'], "calificó la solicitud", $titulo_solicitud, $id_solicitud, $evento);
        require_once(__DIR__ . '/../Controllers/NotificacionC.php');
        $notificacion = new NotificacionC();
        $notificacion->crearNotificacion($id_tecnico, "Has recibido una nueva calificación en la solicitud '$titulo_solicitud'.", 'urgente');
        $_SESSION['tipo_mensaje'] = "success";
        $_SESSION['mensaje'] = "Gracias por compartir tu experiencia.";
        header("Location:Index.php?accion=listarST");
        exit();
    }
}