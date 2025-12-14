<?php
    session_start();
    require 'connbbdd.php';

    // Control de Acceso: Verificar sesión
    if (!isset($_SESSION['user_id'])) {
        header('Location: form_login.php');
        exit();
    }

    $juegoID = $_GET['id'] ?? null;
    $user_id = $_SESSION['user_id'];
    $errores = [];
    $juegoDatos = null; // Variable para almacenar los datos del juego

    if (!$juegoID) {
        $errores[] = 'No se especificó el juego a editar.';
    }

    if (empty($errores)) {
        try {
            // Consulta Segura: Trae el juego y verifica que pertenezca al usuario logueado.
            $sql = "SELECT * FROM juegos WHERE id = :juego_id AND user_id = :user_id LIMIT 1";
            $stmt = $conn->prepare($sql);
            
           
            $stmt->bindParam(':juego_id', $juegoID);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            
            $juegoDatos = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$juegoDatos) {
                $errores[] = 'Error: Este juego no existe o no tienes permiso para editarlo.';
            }

        } catch (PDOException $e) {
            $errores[] = 'Error de BBDD al cargar el juego: ' . $e->getMessage();
        }
    }
    
    // Redirección si hay errores
    if (!empty($errores)) {
        $_SESSION['errores'] = $errores;
        header('Location: dashboard.php');
        exit();
    }

    // Definir categorías para el select 
    $categorias_validas = [
        'accion', 'aventura', 'rol', 'estrategia', 'deportes', 'simulacion', 
        'carreras', 'shooter', 'puzzle', 'mundoAbierto'
    ];
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Editar Juego: <?= htmlspecialchars($juegoDatos['titulo']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body class="bg-light">
    
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">

                <h1 class="text-center mb-4 text-warning">Editar Juego: <?= htmlspecialchars($juegoDatos['titulo']) ?></h1>

                <?php
                    // Muestra errores de redirección en una alerta de Bootstrap
                    if (isset($_SESSION['errores']) && !empty($_SESSION['errores'])) {
                        echo '<div class="alert alert-danger" role="alert">';
                        foreach($_SESSION['errores'] as $error) {
                            echo $error;
                        }
                        echo '</div>';
                        unset($_SESSION['errores']);
                    }
                ?>

                <div class="card shadow-lg p-4 mb-5">
                    <form action="editarJuegoLogica.php" method="POST" enctype="multipart/form-data">
                        
                        <input type="hidden" name="id" value="<?= htmlspecialchars($juegoDatos['id']) ?>">
                        <input type="hidden" name="action" value="update"> 

                        <div class="mb-3">
                            <label for="titulo" class="form-label">Titulo del juego</label>
                            <input type="text" name="titulo" id="titulo" class="form-control" value="<?= htmlspecialchars($juegoDatos['titulo']) ?>">
                        </div>

                        <div class="mb-3">
                            <label for="autor" class="form-label">Autor</label>
                            <input type="text" name="autor" id="autor" class="form-control" value="<?= htmlspecialchars($juegoDatos['autor']) ?>">
                        </div>

                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea name="descripcion" id="descripcion" class="form-control" rows="4"><?= htmlspecialchars($juegoDatos['descripcion']) ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="categoria" class="form-label">Selecciona la categoría del juego</label>
                            <select name="categoria" id="categoria" class="form-select">
                                <?php foreach ($categorias_validas as $cat): ?>
                                    <option value="<?= $cat ?>" <?= ($cat === $juegoDatos['categoria']) ? 'selected' : '' ?>>
                                        <?= ucfirst($cat) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="url" class="form-label">Url</label>
                            <input type="url" name="url" id="url" class="form-control" value="<?= htmlspecialchars($juegoDatos['url']) ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="year_juego" class="form-label">Año de Lanzamiento</label>
                            <select name="year_juego" id="year_juego" class="form-select">
                                <?php
                                    $añoActual = date("Y");
                                    for($i = 1980 ; $i<=$añoActual ; $i++ ){
                                        $selected = ($i == $juegoDatos['year_juego']) ? 'selected' : '';
                                        echo "<option value='{$i}' {$selected}>{$i}</option>";
                                    }
                                ?>
                            </select>
                        </div>

                        <div class="mb-4">
                            <p class="mb-2">Carátula Actual:</p>
                            <img src="<?= htmlspecialchars($juegoDatos['caratula_path']) ?>" alt="Carátula Actual" class="img-thumbnail mb-3" style="width: 150px; height: auto;">
                            
                            <label for="caratula" class="form-label">Cambiar Carátula</label>
                            <input type="file" name="caratula" id="caratula" class="form-control">
                        </div>
                        
                        <div class="d-grid">
                            <input type="submit" name="submit" value="Guardar Cambios" class="btn btn-warning btn-lg">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>