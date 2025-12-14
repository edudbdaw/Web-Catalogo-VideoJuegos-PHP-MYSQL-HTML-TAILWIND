<?php
session_start();
require 'connbbdd.php';

// 1. Seguridad: Verificar sesión
if (!isset($_SESSION['user_id'])) {
    header('Location: form_login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$juegos = [];

try {
    // 2. Consulta SQL: Título, Visualizaciones y Carátula de MIS juegos
    // Ordenamos por visualizaciones DESC (de más a menos visto)
    $sql = "SELECT titulo, visualizaciones, caratula_path, year_juego 
            FROM juegos 
            WHERE user_id = :uid 
            ORDER BY visualizaciones DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':uid', $user_id);
    $stmt->execute();
    
    $juegos = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error = "Error al cargar estadísticas: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estadísticas de mis Juegos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .thumb-stat {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 5px;
        }
    </style>
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-4">
        <div class="container-fluid">
            <a class="navbar-brand text-primary fw-bold" href="dashboard.php">Dashboard</a> 
            <div class="collapse navbar-collapse justify-content-end">
                <ul class="navbar-nav">
                    <li class="nav-item me-3">
                        <a href="dashboard.php" class="btn btn-outline-secondary">Volver al Catálogo</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <h2 class="text-center text-primary mb-4">Rendimiento de tus Juegos</h2>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <?php if (count($juegos) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>Carátula</th>
                                    <th>Juego</th>
                                    <th>Año</th>
                                    <th class="text-center">Visualizaciones</th>
                                    <th class="text-center">Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($juegos as $juego): ?>
                                    <tr>
                                        <td>
                                            <img src="<?= htmlspecialchars($juego['caratula_path']) ?>" class="thumb-stat border">
                                        </td>
                                        <td class="fw-bold text-primary">
                                            <?= htmlspecialchars($juego['titulo']) ?>
                                        </td>
                                        <td><?= htmlspecialchars($juego['year_juego']) ?></td>
                                        <td class="text-center fs-5">
                                            <?php 
                                                $vistas = $juego['visualizaciones'];
                                                $color = $vistas > 10 ? 'success' : ($vistas > 0 ? 'info' : 'secondary');
                                            ?>
                                            <span class="badge bg-<?= $color ?> rounded-pill">
                                                <?= $vistas ?> <i class="bi bi-eye"></i>
                                            </span>
                                        </td>
                                        <td class="text-center text-muted small">
                                            <?= $vistas > 0 ? 'Activo' : 'Sin visitas' ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <h4 class="text-muted">Aún no has subido ningún juego.</h4>
                        <a href="subirJuego.php" class="btn btn-primary mt-3">Subir mi primer juego</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>