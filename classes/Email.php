<?php

namespace Classes;

use PHPMailer\PHPMailer\PHPMailer;

class Email {

    public $nombre;
    public $email;
    public $token;
    public $mail;

    public function __construct($nombre, $email, $token) {
        $this->nombre = $nombre;
        $this->email = $email;
        $this->token = $token;
    }

    public function enviarConfirmacion() {
        // Crear el objeto mail:
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = $_ENV['EMAIL_HOST'];
        $mail->SMTPAuth = true;
        $mail->Port = $_ENV['EMAIL_PORT'];
        $mail->Username = $_ENV['EMAIL_USER'];
        $mail->Password = $_ENV['EMAIL_PASS'];

        $mail->setFrom('cuentas@uptask.com');
        $mail->addAddress('cuentas@uptask.com', 'www.uptask.com');
        $mail->Subject = 'Confirma tu cuenta';

        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        
        $contenido = "<html>";
        $contenido .= "<p>Hola, <strong>" . $this->nombre . "</strong> Has creado tu cuenta en UpTask, solo debes confirmarla presionando el siguiente enlace.</p>";
        $contenido .= "<p><strong>Presiona aquí: </strong><a href='" . $_ENV['APP_URL'] .  "/confirmar?token=" . $this->token . "'>Confirma tu cuenta</a></p>";
        $contenido .= "<p>Sí tú no solicitaste esta cuenta, puedes ignorar este correo.</p>";
        $contenido .= "</html>";
        $mail->Body = $contenido;
        $mail->send();
    }

    public function enviarInstrucciones() {
        // Crear el objeto mail:
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = $_ENV['EMAIL_HOST'];
        $mail->SMTPAuth = true;
        $mail->Port = $_ENV['EMAIL_PORT'];
        $mail->Username = $_ENV['EMAIL_USER'];
        $mail->Password = $_ENV['EMAIL_PASS'];

        $mail->setFrom('cuentas@uptask.com');
        $mail->addAddress('cuentas@uptask.com', 'www.uptask.com');
        $mail->Subject = 'Restablece tu password';

        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        
        $contenido = "<html>";
        $contenido .= "<p>Hola, <strong>" . $this->nombre . "</strong> Has solicitado restablecer tu password, entre al siguiente enlace.</p>";
        $contenido .= "<p><strong>Presiona aquí: </strong><a href='" . $_ENV['APP_URL'] .  "/reestablecer?token=" . $this->token . "'>Restablecer password</a></p>";
        $contenido .= "<p>Sí tú no solicitaste esta cuenta, puedes ignorar este correo.</p>";
        $contenido .= "</html>";
        $mail->Body = $contenido;
        $mail->send();
    }
}