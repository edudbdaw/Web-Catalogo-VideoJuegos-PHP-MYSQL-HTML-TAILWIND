<?php
session_start();
require 'connbbdd.php';

$errores  = [];

//funcion manejos de errores
function manejoErrores($errores_añadir) {
    if (!empty($errores_añadir)) {
        $_SESSION['errores'] = $errores_añadir;
        header('Location:form_login.php');
        exit();
    } 
}

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($username)) {
    $errores[] = 'Debe introducir un usuario';
}

if (strlen($username)<6) {
    $errores [] = 'El usuario debe contener al menos 6 caraacteres';
}

//Comprobacion contraseña
$patron_contraseña = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()-=_+{};:,<.>]).{8,20}$/';
if(!preg_match($patron_contraseña , $password)){
    $errores[] = 'La contraseña debe ser de 8 caracteres minimo , contener un simbolo , mayusculas , minusculas y numeros';
}

manejoErrores($errores);

try {
    $stmt = $conn -> prepare("SELECT id , username , password from usuarios where username = :username");
    $stmt -> bindParam(':username' , $username);
    $stmt -> execute();

    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($password , $usuario['password'])) {
        $_SESSION['user_id'] = $usuario['id'];
        $_SESSION['username'] = $usuario['username'];

        if (isset($_POST['recordarSesion'])) {
            
            // token de sesión único y seguro
            $tiempo = time() + (86400 * 30); // 30 días de duración (86400 segundos = 1 día)
    
            setcookie('recordarSesion', $usuario['username'], $tiempo, '/', '', false, true); 
        }
        header('Location:dashboard.php');
        exit();
    } else {
        $errores[] = "usuario o contraseña incorrectos";
        manejoErrores($errores);
    }
}catch(PDOException $e) {
    $errores [] = "Error al loguearse". $e->getMessage();
    manejoErrores($errores);
}
