<?php
    session_start();
    $inputsB = $_SESSION['inputsB'] ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body class="bg-light">
    
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6"> 
                
                <h1 class="text-center mb-4 text-primary">Registro Usuarios</h1>
                
                <?php
                    // Muestra los errores en un componente de alerta de Bootstrap
                    if (isset($_SESSION['errores']) && !empty($_SESSION['errores'])) {
                        echo '<div class="alert alert-danger" role="alert">';
                        foreach ($_SESSION['errores'] as $error) {
                            echo "<p class='mb-0'>$error</p>";
                        }
                        echo '</div>';
                        
                        unset($_SESSION['errores']); 
                    }
                ?>
                
                <div class="card shadow-lg p-4">

                    <form action="registro.php" method="post" enctype = "multipart/form-data">
                        
                        <div class="mb-3">
                            <label for="username" class="form-label">Escribe un nombre de usuario:</label>
                            <input type="text" id="username" name="username" class="form-control" value=<?php echo htmlspecialchars($inputsB['username'] ?? '')?> >
                        </div>

                        <div class="mb-3">
                            <label for="correoUser" class="form-label">Escribe tu correo electronico</label>
                            <input type="email" name="correoUser" id="correoUser" class="form-control" value=<?php echo htmlspecialchars($inputsB['correoUser'] ?? '')?> >
                        </div>
                        
                        <div class="mb-3">
                            <label for="password1" class="form-label">Escribe la contraseña</label>
                            <input type="password" name="password1" id="password1" class="form-control">
                        </div>
                        
                        <div class="mb-3">
                            <label for="password2" class="form-label">Repite la contraseña</label>
                            <input type="password" name="password2" id="password2" class="form-control">
                        </div>
                        
                        <div class="mb-4">
                            <label for="perfilPicture" class="form-label">Sube tu foto de perfil (Opcional)</label>
                            <input type="file" name="fotoPerfil" id="fotoPerfil" class="form-control">
                        </div>
                        
                        <div class="d-grid">
                            <input type="submit" class="btn btn-success" value="Registrar">
                        </div>
                        
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>