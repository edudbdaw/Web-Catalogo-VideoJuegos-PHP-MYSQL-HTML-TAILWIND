<?php
session_start();
//verificar si hay una session existente
if (!isset($_SESSION['user_id'])) {
$errores [] = 'No hay una sesion iniciada , logueate';
$_SESSION['errores'] = $errores;
header('Location:form_login.php');
exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>FORMULARIO SUBIR JUEGOS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">

                <h1 class="text-center mb-4 text-info">¿Que juego quieres subir?</h1>
                
                <?php
                    // Muestra los errores en un componente de alerta de Bootstrap
                    if (isset($_SESSION['errores']) && !empty($_SESSION['errores']) ) {
                        echo '<div class="alert alert-danger" role="alert">';
                        foreach ($_SESSION['errores'] as $error) {
                            echo "$error<br>";
                        }
                        echo "</div>";
                    }
                    unset($_SESSION['errores']);
                ?>
                
                <div class="card shadow-lg p-4 mb-5">
                    
                    <form method="post" action="subirJuegoLogica.php" enctype = "multipart/form-data">
                        
                        <div class="mb-3">
                            <label for="titulo" class="form-label">Titulo del juego</label>
                            <input type="text" name="titulo" id="titulo" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label for="autor" class="form-label">Autor</label>
                            <input type="text" name="autor" id="autor" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label for="Categoria" class="form-label">Seleciona la categoria del juego</label>
                            <select name="categoria" id="categoria" class="form-select">
                                <option value="accion">Accion</option>
                                <option value="aventura">Aventura</option>
                                <option value="rol">RPG</option>
                                <option value="estrategia">Estrategia</option>
                                <option value="deportes">Deportes</option>
                                <option value="simulacion">Simulacion</option>
                                <option value="carreras">Carreras</option>
                                <option value="shooter">Shooter</option>
                                <option value="puzzle">Puzzle</option>
                                <option value="mundoAbierto">Mundo Abierto</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripcion</label>
                            <textarea id="descripcion" name="descripcion" class="form-control" rows="4"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="url" class="form-label">Url</label>
                            <input type="url" name="url" id="url" class="form-control" placeholder="https://ejemplo.com">
                        </div>

                        <div class="mb-4">
                            <label for="year_juego" class="form-label">Año</label>
                            <select name="year_juego" id="year_juego" class="form-select">
                                <?php
                                    $añoActual = date("Y");
                                    for($i = 1980 ; $i<=$añoActual ; $i++ ){
                                        echo "<option value=$i>$i</option>";
                                    }
                                ?>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="caratula" class="form-label">Caratula</label>
                            <input type="file" name="caratula" id="caratula" class="form-control">
                        </div>
                        
                        <div class="d-grid">
                            <input type="submit" name="submit" id="submit" class="btn btn-primary btn-lg" value="Subir Juego">
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>