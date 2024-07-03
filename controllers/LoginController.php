<?php

namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;

class LoginController {

    public static function login(Router $router) {
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth = new Usuario($_POST);
            $alertas = $auth->validarLogin();

            if(empty($alertas)) {
                $usuario = Usuario::where('email', $auth->email);
                
                if($usuario) {
                    if($usuario->usuarioConfirmado() and $usuario->comprobarPassword($auth->password)) {
                        isSession();
                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;
                        header('Location: /dashboard');
                    }
                } else {
                    Usuario::setAlerta('error', 'Usuario no encontrado');
                }
            }
        }

        $alertas = Usuario::getAlertas();

        // Render a la vista:
        $router->render('auth/login', [
            'titulo' => 'Iniciar Sesión',
            'alertas' => $alertas
        ]);
    }

    public static function logout() {
        isSession();

        $_SESSION = [];

        header('Location: /');
    }

    public static function crear(Router $router) {
        $usuario = new Usuario;
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validar();

           if(empty($alertas)) {
                $existeUsuario = Usuario::where('email', $usuario->email);

                if($existeUsuario) {
                    Usuario::setAlerta('error', 'Usuario ya esta registrado');
                } else {
                    // Hashear password:
                    $usuario->hashPassword();
                    // Eliminar password2:
                    unset($usuario->password2);
                    // Generar token:
                    $usuario->crearToken();
                    // Crear un nuevo usuario:
                    $resultado = $usuario->guardar();
                    
                    if($resultado) {
                        $email = new Email($usuario->nombre, $usuario->email, $usuario->token);
                        $email->enviarConfirmacion();
                        header('Location: /mensaje');
                    }
                }
           }
        }

        $alertas = Usuario::getAlertas();

        // Render a la vista:
        $router->render('auth/crear', [
            'titulo' => 'Crea tu cuenta en UpTask',
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }

    public static function olvide(Router $router) {
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth = new Usuario($_POST);
            $alertas = $auth->validarEmail();

            if(empty($alertas)) {
                $usuario = Usuario::where('email', $auth->email);

                if($usuario) {
                    if($usuario->usuarioConfirmado()) {
                        $usuario->crearToken();
                        unset($usuario->password2);
                        $usuario->guardar();

                        $email = new Email($usuario->nombre, $usuario->email, $usuario->token);
                        $email->enviarInstrucciones();
                        Usuario::setAlerta('exito', 'Hemos enviado las instrucciones a tu email');
                    }
                } else {
                    Usuario::setAlerta('error', 'Usuario no encontrado');
                }
            }
        }

        $alertas = Usuario::getAlertas();

        // Render a la vista:
        $router->render('auth/olvide', [
            'titulo' => 'Olvide mi password',
            'alertas' => $alertas
        ]);
    }

    public static function restablecer(Router $router) {
        $alertas = [];
        $mostrar = true;
        $token = s($_GET['token']);
        if(!$token) header('Location: /');
        $usuario = Usuario::where('token', $token);

        if(!$usuario) {
            Usuario::setAlerta('error', 'Token no válido');
            $mostrar = false;
        }

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarPassword();
            
            if(empty($alertas)) {
                $usuario->hashPassword();
                $usuario->token = null;
                unset($usuario->password2);
                $resultado = $usuario->guardar();

                if($resultado) {
                    header('Location: /');
                }
            }
        }

        $alertas = Usuario::getAlertas();
        // Render a la vista:
        $router->render('auth/reestablecer', [
            'titulo' => 'Restablecer password',
            'alertas' => $alertas,
            'mostrar' => $mostrar
        ]);
    }

    public static function mensaje(Router $router) {
        // Render a la vista:
        $router->render('auth/mensaje', [
            'titulo' => 'Cuenta creada exitosamente'
        ]);
    }

    public static function confirmar(Router $router) {
        $alertas = [];
        $token = s($_GET['token']);
        if(!$token) header('Location: /');
        $usuario = Usuario::where('token', $token);

        if(empty($usuario)) {
            Usuario::setAlerta('error', 'Token no válido');
        } else {
            $usuario->confirmado = 1;
            $usuario->token = null;
            unset($usuario->password2);
            $usuario->guardar();
            Usuario::setAlerta('exito', 'Cuenta comprobada correctamente');
        }

        $alertas = Usuario::getAlertas();

        // Render a la vista:
        $router->render('auth/confirmar', [
            'titulo' => 'Confirmar tu cuenta UpTask',
            'alertas' => $alertas,
            'usuario' => $usuario
        ]);
    }

}