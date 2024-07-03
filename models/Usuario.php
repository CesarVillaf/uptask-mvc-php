<?php

namespace Model;

class Usuario extends ActiveRecord {

    protected static $tabla = 'usuarios';
    protected static $columnasDB = ['id', 'nombre', 'email', 'password', 'token', 'confirmado'];

    public $id;
    public $nombre;
    public $email;
    public $password;
    public $password2; // Atributo temporal
    public $password_actual; // Atributo temporal
    public $password_nuevo; // Atributo temporal
    public $token;
    public $confirmado;

    public function __construct($args = []) {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->password = $args['password'] ?? '';
        $this->password2 = $args['password2'] ?? '';
        $this->password_actual = $args['password_actual'] ?? '';
        $this->password_nuevo = $args['password_nuevo'] ?? '';
        $this->token = $args['token'] ?? '';
        $this->confirmado = $args['confirmado'] ?? 0;
    }

    public function nuevo_password(): array {
        if(!$this->password_actual) {
            self::$alertas['error'][] = 'El password actual es obligatorio';
        }

        if(!$this->password_nuevo) {
            self::$alertas['error'][] = 'El password nuevo es obligatorio';
        }

        if($this->password_nuevo and strlen($this->password_nuevo) < 6) {
            self::$alertas['error'][] = 'El password nuevo debe contener al menos 6 caracteres';
        }

        return self::$alertas;

    }

    public function validarCambios(): array {
        if(!$this->nombre) {
            self::$alertas['error'][] = 'El nombre del usuario es obligatorio';
        }

        if(!$this->email) {
            self::$alertas['error'][] = 'El email del usuario es obligatorio';
        } else {
            if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
                self::$alertas['error'][] = 'Email no válido';
            }
        }
        
        return self::$alertas;
    }

    public function validarLogin(): array {
        if(!$this->password or
            !$this->email or
            !filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            self::$alertas['error'][] = 'El email o password son incorrectos';
        }

        return self::$alertas;
    }

    public function validarPassword(): array {
        if(!$this->password) {
            self::$alertas['error'][] = 'El password del usuario es obligatorio';
        }

        if($this->password and strlen($this->password) < 6) {
            self::$alertas['error'][] = 'El password debe contener al menos 6 caracteres';
        }

        return self::$alertas;
    }

    public function validarEmail(): array {
        if(!$this->email) {
            self::$alertas['error'][] = 'El email del usuario es obligatorio';
        } else {
            if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
                self::$alertas['error'][] = 'Email no válido';
            }
        }

        return self::$alertas;
    }

    public function validar(): array {
        if(!$this->nombre) {
            self::$alertas['error'][] = 'El nombre del usuario es obligatorio';
        }

        if(!$this->email) {
            self::$alertas['error'][] = 'El email del usuario es obligatorio';
        } else {
            if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
                self::$alertas['error'][] = 'Email no válido';
            }
        }
        
        if(!$this->password) {
            self::$alertas['error'][] = 'El password del usuario es obligatorio';
        }
        
        if($this->password and strlen($this->password) < 6) {
            self::$alertas['error'][] = 'El password debe contener al menos 6 caracteres';
        } else if($this->password !== $this->password2) {
            self::$alertas['error'][] = 'Los password son diferentes';
        }

        return self::$alertas;
    }

    // Hashea password:
    public function hashPassword() {
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    }

    public function crearToken() {
        $this->token = uniqid();
    }

    public function usuarioConfirmado(): bool {
        $resultado = true;

        if($this->confirmado === '0') {
            self::$alertas['error'][] = 'Tu cuenta no ha sido confirmada';
            $resultado = false;
        }
        
        return $resultado;
    }

    public function comprobarPassword($password) {
        $resultado = password_verify($password, $this->password);

        if(!$resultado) {
            self::$alertas['error'][] = 'Password incorrecto';
        }

        return $resultado;
    }
}