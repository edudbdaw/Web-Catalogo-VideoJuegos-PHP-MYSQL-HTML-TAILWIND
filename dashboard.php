<?php
session_start();
require 'connbbdd.php';


if (!isset($_SESSION['user_id'])) {
  header('Location:form_login.php');
  exit();
} else {
  $user_id = $_SESSION['user_id'];
}
//Recoger datos de todos los juegos para mostrar
try {
  $stmt = $conn->prepare("SELECT * from juegos");
  $stmt->execute();

  $juego = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  $errores[] = 'Error al recoger los datos de los juegos';
  $_SESSION['errores'] = $errores;
}
//foto de perfil user
try {
  $stmt_user = $conn->prepare("SELECT profile_pic_path FROM usuarios WHERE id = :user_id LIMIT 1");
  $stmt_user->bindParam(':user_id', $user_id);
  $stmt_user->execute();
  $user_data = $stmt_user->fetch(PDO::FETCH_ASSOC);
  $profile_pic_path = $user_data['profile_pic_path'] ?? 'path/to/default/pic.jpg'; // Usar un valor por defecto si no existe
} catch (PDOException $e) {
  $errores[] = 'Error al cargar la foto de perfil: ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DASHBOARD</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <style>
    /* Sobrescribo el estilo de imagen para el grid de juegos */
    .game-cover {
      width: 100%;
      height: 150px;
      /* Altura fija para el contenedor en la tarjeta */
      object-fit: cover;
      border-radius: 0.25rem;
      /* Pequeño redondeo */
    }

    /* El resto de estilos CSS nativos se pueden reemplazar por clases de Bootstrap, pero los mantengo para respetar la consigna */
    .profile-circle {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      /* Hace que la imagen sea circular */
      object-fit: cover;
      margin-left: 10px;
      vertical-align: middle;
    }
  </style>
</head>

<body>

  <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container-fluid">
      <a class="navbar-brand text-primary fw-bold" href="#">Dashboard</a>

      <div class="collapse navbar-collapse justify-content-end">
        <ul class="navbar-nav">

          <li class="nav-item me-3">
            <a href="estadisticas.php" class="btn btn-info text-white">ESTADISTICAS</a>
          </li>
          <li class="nav-item me-3">
            <a href="subirJuego.php" class="btn btn-outline-primary">AÑADIR JUEGO</a>
          </li>

          <li class="nav-item d-flex align-items-center">
            <span class="navbar-text me-2">Bienvenido <strong class="text-dark"><?php echo $_SESSION['username'] ?></strong></span>
            <a href="editarPerfil.php" class="d-flex align-items-center">
              <img src="<?php echo htmlspecialchars($profile_pic_path); ?>" alt="Foto de perfil" class="profile-circle border border-primary">
            </a>
            <a href="logout.php" class="btn btn-sm btn-danger ms-3">[LOGOUT]</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container mt-4">

    <h1 class="mb-4 text-center">Catálogo de Juegos</h1>
    <div class="row justify-content-center mb-4">
      <div class="col-md-6">
        <input type="text" id="buscador" class="form-control" placeholder="Buscar juego por título o autor...">
      </div>
    </div>
    <?php
    // Muestro los errores que surgan de la toma de datos en una alerta
    if (isset($_SESSION['errores']) && !empty($_SESSION['errores'])) {
      echo '<div class="alert alert-danger" role="alert">';
      foreach ($_SESSION['errores'] as $error) {
        echo $error . '<br>';
      }
      echo "</div>";
    }
    unset($_SESSION['errores']);
    ?>

    <div id="contenedor-juegos" class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
      <?php
      // Contenedor para cada juego: Usamos una Card de Bootstrap
      foreach ($juego as $juegoDatos) {
        $juego_user_id = $juegoDatos['user_id'];
        $juegoID = $juegoDatos['id'];
        $juegoNombre = $juegoDatos['titulo'];
        $juegoAutor = $juegoDatos['autor'];
        $juegoDescripcion = $juegoDatos['descripcion'];
        $juegoCaratulaFoto = $juegoDatos['caratula_path'];
        $juegoCategoria = $juegoDatos['categoria'];
        $juegoUrl = $juegoDatos['url'];
        $juegoYear = $juegoDatos['year_juego'];
      ?>
        <div class="col">
          <div class="card h-100 shadow-sm border-0">
            <img src="<?php echo htmlspecialchars($juegoCaratulaFoto); ?>" alt="Carátula del juego" class="card-img-top game-cover">

            <div class="card-body">
              <h5 class="card-title text-primary"><a href='detalleJuego.php?id=<?php echo $juegoID; ?>' class="text-decoration-none"><?php echo $juegoNombre; ?></a></h5>
              <h6 class="card-subtitle mb-2 text-muted"><?php echo $juegoAutor . ' (' . $juegoYear . ')'; ?></h6>
              <p class="card-text text-truncate"><?php echo $juegoDescripcion; ?></p>

              <span class="badge bg-secondary"><?php echo $juegoCategoria; ?></span>
            </div>

            <div class="card-footer bg-transparent border-0 d-flex justify-content-between">
              <a href="<?php echo $juegoUrl; ?>" class="btn btn-sm btn-outline-dark" target="_blank">Visitar URL</a>

              <?php if ($_SESSION['user_id'] == $juego_user_id) : ?>
                <div class="ms-auto">
                  <a href='editarJuego.php?id=<?php echo $juegoID; ?>' class="btn btn-sm btn-warning me-2">Editar</a>
                  <a href='eliminarJuego.php?id=<?php echo $juegoID; ?>' class="btn btn-sm btn-danger">Eliminar</a>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php } ?>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  <script>
    document.getElementById('buscador').addEventListener('keyup', function() {
      let textoBusqueda = this.value;
      let contenedor = document.getElementById('contenedor-juegos');

      // Usamos Fetch API (el estándar moderno para AJAX)
      let formData = new FormData();
      formData.append('busqueda', textoBusqueda);

      fetch('buscarJuegos.php', {
          method: 'POST',
          body: formData
        })
        .then(response => response.text()) // Esperamos HTML como texto
        .then(data => {
          contenedor.innerHTML = data; // Reemplazamos el contenido actual con el nuevo
        })
        .catch(error => console.error('Error:', error));
    });
  </script>
</body>

</html>