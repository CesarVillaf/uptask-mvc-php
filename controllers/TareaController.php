<?php

namespace Controllers;

use Model\Proyecto;
use Model\Tarea;
use MVC\Router;

class TareaController {

    public static function index() {
        isSession();
        isAuth();

        $proyectoId = $_GET['id'];

        if(!$proyectoId) {
            header('Location: /dashboard');
        }

        $proyecto = Proyecto::where('url', $proyectoId);

        if(!$proyecto or $proyecto->propietarioId !== $_SESSION['id']) {
            header('Location: /404');
        }

        $tareas = Tarea::belongsTo('proyectoId', $proyecto->id);
        
        echo json_encode(['tareas' => $tareas]);
    }
    
    public static function crear() {

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            isSession();
            isAuth();

            $url = $_POST['url'];
            $proyecto = Proyecto::where('url', $url);
         
            if(!$proyecto or $proyecto->propietarioId !== $_SESSION['id']) {
                $respuesta = [
                    'tipo' => 'error',
                    'mensaje' => 'Hubo un error al agregar la tarea'
                ];

                echo json_encode($respuesta);
            } else {
                $tarea = new Tarea($_POST);
                $tarea->proyectoId = $proyecto->id;
                $resultado = $tarea->guardar();
                $respuesta = [
                    'tipo' => 'exito',
                    'id' => $resultado['id'],
                    'mensaje' => 'Tarea creada correctamente',
                    'proyectoId' => $tarea->proyectoId
                ];

                echo json_encode($respuesta);
            }
        }
    }

    public static function actualizar() {
        isSession();
        isAuth();

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $proyecto = Proyecto::where('url', $_POST['url']);

            if(!$proyecto or $proyecto->propietarioId !== $_SESSION['id']) {
                $respuesta = [
                    'tipo' => 'error',
                    'mensaje' => 'Hubo un error al actualizar la tarea'
                ];
                echo json_encode($respuesta);
            } else {
                $tarea = new Tarea($_POST);
                $tarea->proyectoId = $proyecto->id;
                $resultado = $tarea->guardar();

                if($resultado) {
                    $respuesta = [
                        'tipo' => 'exito',
                        'mensaje' => 'Tarea actualizada correctamente',
                        'id' => $tarea->id,
                        'proyectoId' => $tarea->proyectoId
                    ];

                    echo json_encode($respuesta);
                }
            }
        }
    }

    public static function eliminar(Router $router) {
        isSession();
        isAuth();

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $proyecto = Proyecto::where('url', $_POST['url']);

            if(!$proyecto or $proyecto->propietarioId !== $_SESSION['id']) {
                $respuesta = [
                    'tipo' => 'error',
                    'mensaje' => 'Hubo un error al eliminar la tarea'
                ];
                echo json_encode($respuesta);
            } else {
                $tarea = new Tarea($_POST);
                $tarea->proyectoId = $proyecto->id;
                $resultado = $tarea->eliminar();

                if($resultado) {
                    $respuesta = [
                        'tipo' => 'exito',
                        'mensaje' => 'Tarea eliminada correctamente',
                        'resultado' => $resultado                    ];

                    echo json_encode($respuesta);
                }
            }
        }
    }
}