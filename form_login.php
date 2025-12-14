<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>

<body class="bg-light">

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">

                <?php
                // Muestra los errores en un componente de alerta de Bootstrap
                if (isset($_SESSION['errores']) && !empty($_SESSION['errores'])) {
                    echo '<div class="alert alert-danger" role="alert">';
                    foreach ($_SESSION['errores'] as $error) {
                        echo "<div>$error<br></div>";
                    }
                    echo '</div>';
                }
                unset($_SESSION['errores']);
                ?>

                <div class="card shadow-lg p-3">
                    <h1 class="card-title text-center text-primary mb-4">LOGIN</h1>

                    <form action="login.php" method="post">

                        <div class="mb-3">
                            <label for="username" class="form-label">Usuario</label>
                            <input type="text" name="username" id="username" class="form-control" onkeyup="verificarUsuario()">
                            <span id="userMsg" class="form-text text-danger"></span>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="password" name="password" id="password" class="form-control" onkeyup="verificarPassword()">
                            <span id="passMsg" class="form-text text-danger"></span>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" name="recordarSesion" id="recordarSesion" class="form-check-input">
                            <label for="recordarSesion" class="form-check-label">Recordar sesión</label>
                        </div>

                        <div class="d-grid gap-2 mb-3">
                            <input type="submit" value="Entrar" class="btn btn-primary">
                        </div>
                        
                        <div class="text-center">
                            <a href="form_registro.php" class="btn btn-link">Regístrate</a>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function verificarUsuario() {
            let username = document.getElementById('username').value;
            let xhr = new XMLHttpRequest();
            xhr.open('POST', 'verificar_login.php', true);
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    document.getElementById('userMsg').innerHTML = xhr.responseText;
                }
            };
            xhr.send('username=' + encodeURIComponent(username));
        }

        function verificarPassword() {
            let password = document.getElementById('password').value;
            let xhr = new XMLHttpRequest();
            xhr.open('POST', 'verificar_login.php', true);
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    document.getElementById('passMsg').innerHTML = xhr.responseText;
                }
            };
            xhr.send('password=' + encodeURIComponent(password));
        }
    </script>
</body>

</html>