<?php

require 'connbbdd.php';
session_start();

//manejo errores 
$errores = [];

function manejoErrores($errores_añadir) {
    if (!empty($errores_añadir)) {
        $_SESSION['errores'] = $errores_añadir;
        header('Location:subirJuego.php');
        exit();
    } 
}

//variables
$titulo = $_POST['titulo'];
$autor = $_POST['autor'];
$categoria = $_POST['categoria'];
$url = $_POST['url'];
$descripcion = trim($_POST['descripcion']);
$year_juego = $_POST['year_juego'];


//Validaciones
if(empty($titulo) || empty($autor) || empty($categoria) || empty($url)) {
    $errores = ['Debe rellenar todos los campos'];
    manejoErrores($errores);
}

$mayusculas = '/[A-Z]/';
if (!preg_match($mayusculas , $titulo)) {
    $errores [] = 'Titulo debe contener mayusculas';
}

if (!preg_match($mayusculas , $autor)) {
    $errores [] = 'El autor debe contener mayusculas';
}

if (!filter_var($url, FILTER_VALIDATE_URL)) {
    $errores [] = 'Url no valida';
}

$categorias_validas = ['accion' , 'aventura', 'rol', 'estrategia', 'deportes', 'simulacion', 'carreras', 'shooter', 'puzzle', 'mundoAbierto'];

if(!in_array($categoria , $categorias_validas)) {
    $errores = 'Categoria introducida no valida';
}

if (strlen($descripcion)>2000) {
    $errores[] = 'La descripcion no puede superar los 2000 caracteres';
}

manejoErrores($errores);


//Manejo de ruta caratula
define('RUTA_POR_DEFECTO' , 'subidas/caratulas/caratuladefault.png');
$caratulaRuta = RUTA_POR_DEFECTO;

if (isset($_FILES['caratula']) && $_FILES['caratula']['error']=== UPLOAD_ERR_OK) {
    $caratula = $_FILES['caratula'];

    if ($caratula['size'] > 2097152) { //2MB
        $error[] = 'El tamaño de la imagen es superior a 2MB';
    }

    $formatosValidos = ['image/jpeg', 'image/png', 'image/gif'];

    if (!in_array($caratula['type'] , $formatosValidos)) {
        $errores [] = 'El formato de la imagen no es valido . Solo JPG , PNG o GIF';
    }

    if (empty($error)) {
        $extension_caratula = pathinfo($caratula['name'], PATHINFO_EXTENSION);
        $nombreUnico = uniqid().'.'.$extension_caratula;
        $rutaDestino = 'subidas/caratulas/'.$nombreUnico;

        if (move_uploaded_file($caratula['tmp_name'], $rutaDestino)) {
            //Si hay exito actualizamos la ruta de la caratula a la que creamos
            $caratulaRuta = $rutaDestino;
        }else {
            $errores [] = 'No se pudo guardar el archivo';
            manejoErrores($errores);
        }
    }
}


try {
    $stmt = $conn -> prepare('INSERT INTO juegos (user_id , titulo , descripcion, autor, caratula_path, categoria, url, year_juego) VALUES(:user_id , :titulo, :descripcion, :autor, :caratula_path, :categoria, :url, :year_juego)');
    $stmt -> bindParam(':user_id', $_SESSION['user_id']);
    $stmt -> bindParam(':titulo', $titulo);
    $stmt -> bindParam(':descripcion' , $descripcion);
    $stmt -> bindParam(':autor', $autor);
    $stmt -> bindParam(':caratula_path' , $caratulaRuta);
    $stmt -> bindParam(':categoria' , $categoria);
    $stmt -> bindParam(':url', $url);
    $stmt -> bindParam(':year_juego', $year_juego);
    $stmt -> execute();

    header('Location:dashboard.php');
    exit();

} catch (PDOException $e) {
    $errores[] = 'Error al insertar el juego'. $e->getMessage();
}

manejoErrores($errores);
//ultima verificacion
