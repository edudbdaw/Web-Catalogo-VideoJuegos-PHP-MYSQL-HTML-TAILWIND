<?php
session_start();
require 'connbbdd.php';

if(!isset($_SESSION['user_id'])){
    $errores[] = 'No existe session';
    manejoErrores($errores);
}
$errores = [];

function manejoErrores($errores_añadir) {
    if (!empty($errores_añadir)) {
        $_SESSION['errores'] = $errores_añadir;
        header('Location: editarPerfil.php');
        exit();
    } 
}


$logged_user_id = $_SESSION['user_id'] ?? null;
$user_id_form = $_POST['user_id'] ?? null; 
$username_original = $_POST['username'] ?? '';
$email_original = $_POST['email'] ?? '';       

define('RUTA_POR_DEFECTO', 'subidas/perfil/default.png');

$fotoPerfilRuta = RUTA_POR_DEFECTO; 

if (isset($_FILES['fotoPerfil']) && $_FILES['fotoPerfil']['error'] === UPLOAD_ERR_OK ) {
    
    $fotoPerfil = $_FILES['fotoPerfil'];

    if ($fotoPerfil['size'] > 2097152) {
        $errores[] = 'La foto es superior a 2MB.';
    }

    $formatosValidos = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($fotoPerfil['type'], $formatosValidos)) {
        $errores[] = 'Formato de archivo no permitido. Solo JPG, PNG o GIF.';
    }
    
    manejoErrores($errores);

    //Despues de manejar errores , y verificar , creamos la nueva ruta
    $extensionFoto = pathinfo($fotoPerfil['name'], PATHINFO_EXTENSION);
    $nombre_unico = uniqid() . '.' . $extensionFoto;
    $rutaDestino = 'subidas/perfil/'. $nombre_unico;
    if (move_uploaded_file($fotoPerfil['tmp_name'], $rutaDestino)) {
        $fotoPerfilRuta = $rutaDestino;
    } else {
        $errores[] = 'No se pudo guardar la nueva foto en el servidor.';
        manejoErrores($errores);
    }
} 

try {
    $sql = "UPDATE usuarios SET profile_pic_path = :caratula_path WHERE id = :user_id";
    
    $stmt = $conn->prepare($sql);
    
    // Vinculación de parámetros
    $stmt->bindParam(':user_id', $logged_user_id);
    $stmt->bindParam(':caratula_path', $fotoPerfilRuta); // Usamos la nueva ruta o la antigua
    
    
    $stmt->execute();

    $_SESSION['mensaje_exito'] = "Foto de perfil actualizada correctamente.";
    header('Location: dashboard.php');
    exit();

} catch (PDOException $e) {
    $errores[] = 'Error fatal de BBDD al actualizar: ' . $e->getMessage();
    manejoErrores($errores);
}