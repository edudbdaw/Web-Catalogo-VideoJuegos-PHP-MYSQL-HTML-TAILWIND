<?php
session_start();
require 'connbbdd.php';

$errores = [];

function manejoErrores($errores_añadir) {
    if (!empty($errores_añadir)) {
        $_SESSION['errores'] = $errores_añadir;
        header('Location: dashboard.php');
        exit();
    } 
}

// Variables POST y de Sesión
$juegoID = $_POST['id'] ?? null;
$user_id = $_SESSION['user_id'] ?? null;

$titulo = $_POST['titulo'] ?? '';
$autor = $_POST['autor'] ?? '';
$categoria = $_POST['categoria'] ?? '';
$url = $_POST['url'] ?? '';
$descripcion = trim($_POST['descripcion'] ?? '');
$year_juego = $_POST['year_juego'] ?? '';

// validar datos

if (!$user_id || !$juegoID) {
    $errores[] = ['Acceso denegado o ID de juego faltante.'];
    manejoErrores($errores);
}

if (empty($titulo) || empty($autor) || empty($categoria) || empty($descripcion) || empty($year_juego)) {
    $errores[] = 'Debe rellenar todos los campos obligatorios.';
}

$mayusculas = '/[A-Z]/'; 
if (!preg_match($mayusculas, $titulo)) {
    $errores[] = 'El título debe contener al menos una mayúscula.';
}
if (!filter_var($url, FILTER_VALIDATE_URL)) {
    $errores[] = 'Url no válida.';
}

$categorias_validas = ['accion' , 'aventura', 'rol', 'estrategia', 'deportes', 'simulacion', 'carreras', 'shooter', 'puzzle', 'mundoAbierto'];

if(!in_array($categoria , $categorias_validas)) {
    $errores = 'Categoria introducida no valida';
}

if (strlen($descripcion)>2000) {
    $errores[] = 'La descripcion no puede superar los 2000 caracteres';
}

manejoErrores($errores);




// Ruta sera la misma , solo actualizamos la imagen
try {
    $stmt = $conn->prepare("SELECT caratula_path FROM juegos WHERE id = :id AND user_id = :user_id");
    $stmt->execute([':id' => $juegoID, ':user_id' => $user_id]);
    $old_caratula_path = $stmt->fetchColumn(); 
} catch (PDOException $e) {
    $errores[] = 'Error de BBDD al verificar la carátula antigua.';
    manejoErrores($errores);
}

// Si la consulta falló al traer la ruta, asignamos la recuperada o una por defecto segura.
$caratulaRuta = $old_caratula_path ?: 'subidas/caratulas/caratuladefault.png'; 


if (isset($_FILES['caratula']) && $_FILES['caratula']['error'] === UPLOAD_ERR_OK) {
    
    $caratula = $_FILES['caratula'];
    
    if ($caratula['size'] > 2097152) { //2MB
        $errores[] = 'El tamaño de la imagen es superior a 2MB';
    }

    $formatosValidos = ['image/jpeg', 'image/png', 'image/gif'];

    if (!in_array($caratula['type'] , $formatosValidos)) {
        $errores [] = 'El formato de la imagen no es valido . Solo JPG , PNG o GIF';
    }

    manejoErrores($errores);
    $extension_caratula = pathinfo($caratula['name'], PATHINFO_EXTENSION);
    $nombre_unico = uniqid() . '.' . $extension_caratula;
    $rutaDestinoSistema = 'subidas/caratulas/' . $nombre_unico; // Ruta para el disco
    $caratulaRuta = '/subidas/caratulas/' . $nombre_unico;       // Ruta para la BBDD (WEB)

    if (move_uploaded_file($caratula['tmp_name'], $rutaDestinoSistema)) {
        $caratulaRuta = $rutaDestinoSistema;
    } else {
        $errores[] = 'Error al guardar la nueva carátula en el servidor.';
        manejoErrores($errores);
    }
} 
// Si no se subió ningún archivo, $caratulaRuta conserva el valor de $old_caratula_path.


try {
    $sql_update = "UPDATE juegos 
            SET titulo = :titulo, descripcion = :descripcion, autor = :autor, 
                caratula_path = :caratula_path, categoria = :categoria, 
                url = :url, year_juego = :year_juego 
            WHERE id = :id AND user_id = :user_id";
    
    $stmt = $conn->prepare($sql_update);
    
    $stmt->bindParam(':id', $juegoID);
    $stmt->bindParam(':user_id', $user_id); 
    $stmt->bindParam(':titulo', $titulo);
    $stmt->bindParam(':descripcion', $descripcion);
    $stmt->bindParam(':autor', $autor);
    $stmt->bindParam(':caratula_path', $caratulaRuta);
    $stmt->bindParam(':categoria', $categoria);
    $stmt->bindParam(':url', $url);
    $stmt->bindParam(':year_juego', $year_juego);
    $stmt->execute();
    $_SESSION['mensaje_exito'] = "Juego actualizado correctamente.";
    header('Location: dashboard.php');
    exit();

} catch (PDOException $e) {
    $errores[] = 'Error fatal de BBDD al actualizar: ' . $e->getMessage();
    manejoErrores($errores);
}