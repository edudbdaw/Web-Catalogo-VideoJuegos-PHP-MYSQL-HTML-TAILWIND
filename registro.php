<?php
session_start();
require 'connbbdd.php';

//Verificacion de si la base de datos esta funcionando
if (!isset($conn)) {
    echo "error con la base de datos";
    die();
}

$errores = [];

//funcion manejos de errores
function manejoErrores($errores_añadir) {
    if (!empty($errores_añadir)) {
        $_SESSION['errores'] = $errores_añadir;
        header('Location:form_registro.php');
        exit();
    } 
}

//Variables del formulario
$username = $_POST['username'] ?? '';
$correoUser = $_POST['correoUser'] ?? '';
$password1 = $_POST['password1'] ?? '';
$password2= $_POST['password2'] ?? '';


// Validaciones
if (empty($username)) {
    $errores[] = 'Debe introducir un usuario';
}

if (strlen($username)<6) {
    $errores [] = 'El usuario debe contener al menos 6 caraacteres';
}

if (!filter_var($correoUser , FILTER_VALIDATE_EMAIL)) {
    $errores[] = 'Debe introducir un correo electronico valido';
}

// Validaciones contraseñas


if ($password1 !== $password2) {
    $errores[] = 'Las contraseñas no coinciden';
} 

$patron_contraseña = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()-=_+{};:,<.>]).{8,20}$/';
if(!preg_match($patron_contraseña , $password1)){
    $errores[] = 'La contraseña debe ser de 8 caracteres minimo , contener un simbolo , mayusculas y minusculas';
} else {
    $password_hash = password_hash($password1,PASSWORD_DEFAULT);
}


// Manejo de errores en validaciones
if (!empty($errores)) {

    $_SESSION['inputsB'] = [
        'username' => $username,
        'correoUser' => $correoUser
    ];

    manejoErrores($errores);
}

//Subir Foto Perfil
define('RUTA_POR_DEFECTO' , 'subidas/perfil/default.png' );
$fotoPerfilRuta = RUTA_POR_DEFECTO;

//verificamos si error == 0 con UPLOAD_ERR_OK , si no se sube archivo es 4 y no entra en el isset
if (isset($_FILES['fotoPerfil']) && $_FILES['fotoPerfil']['error'] === UPLOAD_ERR_OK ) {
    //Recoger la informacion de el archivo 
    $fotoPerfil = $_FILES['fotoPerfil'];

    if ($fotoPerfil['size'] > 2097152) {
        $errores[] = 'La foto es superoir a 2MB';
    }

    $formatosValidos = ['image/jpeg', 'image/png', 'image/gif'];
    //Verificamos si el typo de la foto de perfil subida es valido con los que guarda nuestro array
    if (!in_array($fotoPerfil['type'], $formatosValidos)) {
        $errores[] = 'Formato de archivo no permitido. Solo JPG, PNG o GIF.';
    }

    if (empty($errores)) {

        //Crear nombre unico si no hay errores
        $extensionFoto = pathinfo($fotoPerfil['name'] , PATHINFO_EXTENSION);
        $nombre_unico = uniqid().'.'.$extensionFoto;
        $rutaDestino = 'subidas/perfil/'.$nombre_unico;

        if(move_uploaded_file($fotoPerfil['tmp_name'] , $rutaDestino)) {
            //Si hay exito
            $fotoPerfilRuta = $rutaDestino;
        } else {
            $errores[] = 'No se pudo guardar el archivo';
            manejoErrores($errores);
        }

    }
}



// Consulta de email y user unico

try {
    $stmt = $conn -> prepare("SELECT COUNT(*) from usuarios where email = :correoUser");
    $stmt ->bindParam(':correoUser' , $correoUser);
    $stmt -> execute();

    if ($stmt -> fetchColumn() > 0) {
        $errores [] = "Error , ya existe el correo electronico";
    }
} catch (PDOException $e) {
    $errores [] = 'Error al verificar el email'. $e->getMessage();
}

try {
    $stmt = $conn -> prepare("SELECT COUNT(*) from usuarios where username = :username");
    $stmt -> bindParam(':username', $username);
    $stmt -> execute();

    if ($stmt->fetchColumn() > 0) {
        $errores[] = "Este usuario ya existe , use otro"; 
    }
} catch (PDOException $e) {
    $errores[] = "Error al verificar el usuario" . $e->getMessage();
}

//Manejamos los errores
manejoErrores($errores);

//Insertar datos en la bbdd

try {
    $stmt = $conn -> prepare("INSERT INTO usuarios (username, email, password, profile_pic_path) VALUES(:username , :correoUser, :password_hash, :fotoPerfil)");
    $stmt -> bindParam(':username' , $username);
    $stmt -> bindParam(':correoUser' ,$correoUser);
    $stmt -> bindParam(':password_hash' , $password_hash);
    $stmt -> bindParam(':fotoPerfil' , $fotoPerfilRuta);
    $stmt -> execute();
    header('Location:form_login.php');
    exit();
} catch (PDOException $e) {
    $errores[] = 'Error al insertar datos'. $e->getMessage();
}

manejoErrores($errores);