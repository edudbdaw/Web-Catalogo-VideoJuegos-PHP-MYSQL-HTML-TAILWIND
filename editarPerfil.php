<?php
session_start();
require 'connbbdd.php';

if (!isset($_SESSION['user_id'])) {
  header('Location: form_login.php');
  exit();
}

$user_id = $_SESSION['user_id'];
$errores = [];
try {
 
  $sql = "SELECT username, email, profile_pic_path FROM usuarios WHERE id = :user_id LIMIT 1";
  $stmt = $conn->prepare($sql);
  
  $stmt->bindParam(':user_id', $user_id);
  $stmt->execute();
  
  $userData = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$userData) {
    
    $errores[] = 'Error: No se encontraron los datos del usuario.';
  }

} catch (PDOException $e) {
  $errores[] = 'Error de BBDD al cargar datos de perfil: ' . $e->getMessage();
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
  <title>Editar Perfil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <style>
    /* Estilo para asegurar que la imagen de perfil se vea bien y sea circular */
    .profile-circle-lg {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      object-fit: cover;
      border: 3px solid #0d6efd; /* Borde de color primario de Bootstrap */
    }
  </style>
</head>
<body class="bg-light">
    
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-7 col-lg-5">

                <h1 class="text-center mb-4 text-info">Editar Perfil de <?= htmlspecialchars($userData['username']) ?></h1>

                <?php
                    // Mostrar errores en una alerta de Bootstrap
                    if (isset($_SESSION['errores']) && !empty($_SESSION['errores'])) {
                        echo '<div class="alert alert-danger" role="alert">';
                        echo implode('<br>', $_SESSION['errores']);
                        echo '</div>';
                        unset($_SESSION['errores']);
                    }
                ?>

                <div class="card shadow-lg p-4 mb-5">
                    <form action="editarPerfilLogica.php" method="POST" enctype="multipart/form-data">
                        
                        <input type="hidden" name="user_id" value="<?= $user_id ?>">
                        <input type="hidden" name="username" value="<?= htmlspecialchars($userData['username']) ?>">
                        <input type="hidden" name="email" value="<?= htmlspecialchars($userData['email']) ?>">

                        <div class="mb-4">
                            <p class="mb-2"><strong>Nombre de Usuario:</strong> 
                                <span class="text-dark bg-light p-1 rounded"><?= htmlspecialchars($userData['username']) ?></span>
                            </p>
                            <p class="mb-0"><strong>Correo Electrónico:</strong> 
                                <span class="text-dark bg-light p-1 rounded"><?= htmlspecialchars($userData['email']) ?></span>
                            </p>
                        </div>
                        
                        <h2 class="h5 mb-3 text-secondary border-bottom pb-2">Gestión de Foto de Perfil</h2>

                        <div class="mb-4 text-center">
                            <p class="mb-2">Foto de Perfil Actual</p>
                            <img src="<?= htmlspecialchars($userData['profile_pic_path']) ?>" 
                     alt="Foto de Perfil" class="profile-circle-lg">
                        </div>
                        
                        <div class="mb-4">
                            <label for="profile_pic" class="form-label">Cambiar Foto de Perfil</label>
                            <input type="file" name="fotoPerfil" id="profile_pic" class="form-control">
                        </div>

                        <div class="d-grid">
                            <input type="submit" name="submit" value="Actualizar Foto" class="btn btn-primary btn-lg">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>