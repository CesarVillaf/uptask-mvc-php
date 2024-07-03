<?php

namespace Controllers;

use Model\Proyecto;
use Model\Usuario;
use MVC\Router;

class DashboardController {

    public static function index(Router $router) {
        isSession();
        isAuth();

        $id = $_SESSION['id'];
        $proyectos = Proyecto::belongsTo('propietarioId', $id);

        $router->render('dashboard/index', [
            'titulo' => 'Proyectos', 
            'proyectos' => $proyectos
        ]);
    }

    public static function crear_proyecto(Router $router) {
        isSession();
        isAuth();
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $proyecto = new Proyecto($_POST);
            $alertas = $proyecto->validar();

            if(empty($alertas)) {
                $proyecto->url = $proyecto->generarHash();
                $proyecto->propietarioId = $_SESSION['id'];
                $proyecto->guardar();

                header('Location: /proyecto?id=' . $proyecto->url);
            }
        }

        $router->render('dashboard/crear-proyecto', [
            'titulo' => 'Crear Proyecto',
            'alertas' => $alertas
        ]);
    }

    public static function proyecto(Router $router) {
        isSession();
        isAuth();

        $id = s($_GET['id']);
        if(!$id) header('Location: /dashboard');
        $proyecto = Proyecto::where('url', $id);

        if($proyecto->propietarioId !== $_SESSION['id']) {
            header('Location: /dashboard');
        }

        $router->render('dashboard/proyecto', [
            'titulo' => $proyecto->proyecto,
        ]);
    }

    public static function perfil(Router $router) {
        isSession();
        isAuth();

        $alertas = [];
        $id = $_SESSION['id'];
        $usuario = Usuario::find($id);

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarCambios();

            if(empty($alertas)) {
                // Verificar que no exista el email:
                $existeUsuario = Usuario::where('email', $usuario->email);

                if($existeUsuario and $existeUsuario->id !== $usuario->id) {
                    Usuario::setAlerta('error', 'Email no válido, ya pertenece a otra cuenta');
                } else {
                    $usuario->guardar();
                    $_SESSION['nombre'] = $usuario->nombre;
                    $_SESSION['email'] = $usuario->email;
                    Usuario::setAlerta('exito', 'Cambios guardados correctamente');
                }

            }
        }

        $alertas = Usuario::getAlertas();

        $router->render('dashboard/perfil', [
            'titulo' => 'Perfil',
            'alertas' => $alertas,
            'usuario' => $usuario
        ]);
    }

    public static function cambiar_password(Router $router) {
        isSession();
        isAuth();

        $alertas = [];
        
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = Usuario::find($_SESSION['id']);
            
            // Sincronizar con los datos del usuario:
            $usuario->sincronizar($_POST);
            $alertas = $usuario->nuevo_password();

            if(empty($alertas)) {
                $resultado = $usuario->comprobarPassword($usuario->password_actual);
                
                if($resultado) {
                    $usuario->password = $usuario->password_nuevo;
                    $usuario->hashPassword();
                    unset($usuario->password2);
                    unset($usuario->password_actual);
                    unset($usuario->password_nuevo);
                    $resultado = $usuario->guardar();

                    if($resultado) {
                        Usuario::setAlerta('exito', 'Password actualizado correctamente');
                    }
                }
            }
        }

        $alertas = Usuario::getAlertas();

        $router->render('dashboard/cambiar-password', [
            'titulo' => 'Cambiar Password',
            'alertas' => $alertas
        ]);

    }
}