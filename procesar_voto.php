<?php
session_start();
require 'connbbdd.php';
header('Content-Type: application/json');

$response = ['success' => false, 'score' => 0, 'error' => ''];

$user_id = $_SESSION['user_id'] ?? null;
$juego_id = $_POST['juego_id'] ?? null;
$voto_tipo = $_POST['voto'] ?? null; 

if (!$user_id) {
    $response['error'] = 'Debe iniciar sesión para votar.';
    echo json_encode($response);
    exit();
}

if (!$juego_id || !in_array($voto_tipo, ['like', 'dislike'])) {
    $response['error'] = 'Datos de voto inválidos.';
    echo json_encode($response);
    exit();
}

$voto_valor = ($voto_tipo === 'like') ? 1 : 0; 

try {
    //  INTENTAR INSERTAR VOTO ÚNICO
    $sql_insert = "INSERT INTO votos (user_id, juego_id, voto) 
                   VALUES (:user_id, :juego_id, :voto_valor)";
    $stmt = $conn->prepare($sql_insert);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':juego_id', $juego_id);
    $stmt->bindParam(':voto_valor', $voto_valor);
    $stmt->execute();

    //  CALCULAR Y DEVOLVER EL NUEVO SCORE 
    $sql_score = "SELECT SUM(CASE WHEN voto = 1 THEN 1 ELSE -1 END) as total_score 
                  FROM votos WHERE juego_id = :juego_id";
    $stmt_score = $conn->prepare($sql_score);
    $stmt_score->bindParam(':juego_id', $juego_id);
    $stmt_score->execute();
    $new_score = $stmt_score->fetchColumn() ?: 0; // Si no hay votos, es 0

    $response['success'] = true;
    $response['score'] = (int)$new_score;

    echo json_encode($response);
    exit();
    
} catch (PDOException $e) {
    // Código 23000 es el error de UNIQUE constraint (voto duplicado)
    if ($e->getCode() == 23000) { 
        $response['error'] = 'Ya has votado por este juego.';
    } else {
        $response['error'] = 'Error de BBDD al votar.';
    }
    echo json_encode($response);
    exit();
}