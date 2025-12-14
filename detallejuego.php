<?php
session_start();
require 'connbbdd.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: form_login.php');
    exit();
}

$juegoID = $_GET['id'] ?? null;
$logged_user_id = $_SESSION['user_id'];
$errores = [];
$juegoDatos = null;

if (!$juegoID) {
    $errores[] = 'Juego no especificado.';
}

if (empty($errores)) {
    try {
        // 1. INCREMENTAR VISUALIZACIONES (Lógica de Contador)
        $sql_inc = "UPDATE juegos SET visualizaciones = visualizaciones + 1 WHERE id = :juego_id";
        $stmt_inc = $conn->prepare($sql_inc);
        $stmt_inc->bindParam(':juego_id', $juegoID);
        $stmt_inc->execute();

        // 2. SELECCIONAR DATOS DEL JUEGO
        $sql = "SELECT id, user_id, titulo, descripcion, autor, caratula_path, categoria, url, year_juego, visualizaciones FROM juegos WHERE id = :juego_id LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':juego_id', $juegoID);
        $stmt->execute();
        $juegoDatos = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$juegoDatos) {
            $errores[] = 'Juego no encontrado.';
        }
        
        // 3. CALCULAR PUNTUACIÓN INICIAL
        $sql_score = "SELECT SUM(CASE WHEN voto = 1 THEN 1 ELSE -1 END) as total_score FROM votos WHERE juego_id = :juego_id";
        $stmt_score = $conn->prepare($sql_score);
        $stmt_score->bindParam(':juego_id', $juegoID);
        $stmt_score->execute();
        $current_score = $stmt_score->fetchColumn() ?: 0;

    } catch (PDOException $e) {
        $errores[] = 'Error de BBDD: ' . $e->getMessage();
    }
}

if (!empty($errores)) {
    $_SESSION['errores'] = $errores;
    header('Location: dashboard.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Detalle: <?= htmlspecialchars($juegoDatos['titulo']) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <style>
        /* Estilo para asegurar que la imagen de carátula se vea bien */
        .game-cover-img {
            max-width: 100%;
            height: auto;
            border-radius: 0.5rem;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
  </style>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-9 col-xl-8">
                
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="text-primary"><?= htmlspecialchars($juegoDatos['titulo']) ?></h1>
                    
                    <?php if ($_SESSION['user_id'] === $juegoDatos['user_id']): ?>
                        <div>
                            <a href='editarJuego.php?id=<?= $juegoID ?>' class="btn btn-warning me-2">[EDITAR]</a>
                            <a href='eliminarJuego.php?id=<?= $juegoID ?>' onclick='return confirm("¿Seguro de eliminar?");' class="btn btn-danger">[ELIMINAR]</a>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="card shadow-lg p-4 mb-4 border-0">
                    <div class="row">
                        
                        <div class="col-md-4 mb-3 mb-md-0 text-center">
                            <p class="mb-0">
                                <img src="<?= htmlspecialchars($juegoDatos['caratula_path']) ?>" alt="Carátula" class="game-cover-img">
                            </p>
                        </div>

                        <div class="col-md-8">
                            <h2 class="h5 text-secondary border-bottom pb-2 mb-3">Detalles</h2>

                            <div class="row mb-4 small">
                                <div class="col-6 col-sm-3 mb-2">
                                    <strong>Autor:</strong> 
                                    <p class="mb-0 text-dark"><?= htmlspecialchars($juegoDatos['autor']) ?></p>
                                </div>
                                <div class="col-6 col-sm-3 mb-2">
                                    <strong>Año:</strong> 
                                    <p class="mb-0 text-dark"><?= htmlspecialchars($juegoDatos['year_juego']) ?></p>
                                </div>
                                <div class="col-6 col-sm-3 mb-2">
                                    <strong>Categoría:</strong> 
                                    <span class="badge bg-info text-dark"><?= htmlspecialchars($juegoDatos['categoria']) ?></span>
                                </div>
                                <div class="col-6 col-sm-3 mb-2">
                                    <strong>Vistas:</strong> 
                                    <p class="mb-0 text-primary fw-bold"><?= htmlspecialchars($juegoDatos['visualizaciones']) ?></p>
                                </div>
                            </div>
                            
                            <h2 class="h5 text-secondary border-bottom pb-2 mb-3">Descripción</h2>
                            <p class="lead text-dark"><?= nl2br(htmlspecialchars($juegoDatos['descripcion'])) ?></p>
                            
                            <?php if (!empty($juegoDatos['url'])): ?>
                                <a href="<?= htmlspecialchars($juegoDatos['url']) ?>" target="_blank" class="btn btn-outline-primary mt-3">
                                    Visitar Página del Juego
                                </a>
                            <?php endif; ?>

                        </div>
                    </div>
                </div>

                <div class="card shadow-sm p-4 border-0">
                    <h2 class="h4 mb-3 text-center">Puntuación</h2>
                    
                    <div class="text-center">
                        <div id="puntuacion-display" class="my-3" style="font-size: 2.5em; font-weight: bold; color: <?= ((int)$current_score >= 0) ? 'var(--bs-success)' : 'var(--bs-danger)'; ?>">
                            Puntuación: <?= (int)$current_score ?>
                        </div>
                        
                        <div class="d-flex justify-content-center gap-3">
                            <button id="like-btn" data-voto="like" data-juego-id="<?= $juegoID ?>" class="btn btn-success btn-lg">👍 Me Gusta</button>
                            <button id="dislike-btn" data-voto="dislike" data-juego-id="<?= $juegoID ?>" class="btn btn-danger btn-lg">👎 No Me Gusta</button>
                        </div>
                        
                        <div id="voto-mensaje" class="mt-3" style="color: var(--bs-danger);"></div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  <script>
        // Tu código JavaScript para votar está aquí. Lo adapto a los nuevos colores de Bootstrap
    document.addEventListener('DOMContentLoaded', function() {
      const likeBtn = document.getElementById('like-btn');
      const dislikeBtn = document.getElementById('dislike-btn');
      const puntuacionDisplay = document.getElementById('puntuacion-display');
      const votoMensaje = document.getElementById('voto-mensaje');
      const juegoId = likeBtn.dataset.juegoId;

      function procesarVoto(votoTipo) {
        // Uso clase de texto de Bootstrap para el mensaje de espera
                votoMensaje.classList.remove('text-danger');
        votoMensaje.classList.add('text-warning');
        votoMensaje.innerHTML = 'Votando...';
        
        fetch('procesar_voto.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: 'juego_id=' + juegoId + '&voto=' + votoTipo
        })
        .then(response => response.json())
        .then(data => {
          votoMensaje.classList.remove('text-warning', 'text-danger', 'text-success');
          votoMensaje.innerHTML = '';
          
          if (data.success) {
                        // Actualiza el texto de la puntuación
            puntuacionDisplay.innerHTML = 'Puntuación: ' + data.score;
                        // Cambia color del score usando clases Bootstrap (green para positivo, red para negativo)
            puntuacionDisplay.style.color = (data.score >= 0) ? 'var(--bs-success)' : 'var(--bs-danger)';
                        votoMensaje.classList.add('text-success');
                        votoMensaje.innerHTML = '¡Voto registrado!';
          } else {
            votoMensaje.classList.add('text-danger');
            votoMensaje.innerHTML = 'Error: ' + data.error;
          }
        })
        .catch(error => {
          votoMensaje.classList.add('text-danger');
          votoMensaje.innerHTML = 'Error de conexión con el servidor.';
        });
      }

      likeBtn.addEventListener('click', () => procesarVoto('like'));
      dislikeBtn.addEventListener('click', () => procesarVoto('dislike'));
    });
  </script>
</body>
</html>