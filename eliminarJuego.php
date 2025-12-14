<?php
session_start();
require 'connbbdd.php';

$juegoID = $_GET['id'] ?? null;
$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    header('Location: form_login.php');
    exit();
}

if (!$juegoID) {
    $_SESSION['errores'] = ['No se especificó el juego a eliminar.'];
    header('Location: dashboard.php');
    exit();
}

try {
    $sql = "DELETE FROM juegos WHERE id = :juego_id AND user_id = :user_id";
    $stmt = $conn->prepare($sql);

    $stmt->bindParam(':juego_id', $juegoID);
    $stmt->bindParam(':user_id', $user_id); 
    
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $_SESSION['mensaje_exito'] = "Juego eliminado correctamente.";
    } else {
        $_SESSION['errores'] = ["Error: No se encontró el juego o no tienes permiso para borrarlo."];
    }
    
} catch (PDOException $e) {
    $_SESSION['errores'] = ["Error de BBDD al eliminar: " . $e->getMessage()];
}

header('Location: dashboard.php');
exit();