<?php
session_start();
require 'connbbdd.php';

// Recogemos el texto que nos manda el JS. Si no hay nada, cadena vacía.
$busqueda = $_POST['busqueda'] ?? '';
$user_id = $_SESSION['user_id'] ?? null;

try {
    // Preparamos la consulta. Usamos LIKE para buscar coincidencias parciales.
    // Buscamos por título O por autor.
    $sql = "SELECT * FROM juegos WHERE titulo LIKE :busqueda OR autor LIKE :busqueda";
    $stmt = $conn->prepare($sql);
    
    // Añadimos los porcentajes para el LIKE
    $parametro = "%" . $busqueda . "%";
    $stmt->bindParam(':busqueda', $parametro);
    $stmt->execute();
    
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Si no hay juegos, mostramos un mensaje
    if (!$resultados) {
        echo '<p class="text-center text-muted">No se encontraron juegos.</p>';
    }

    // Generamos el HTML de las tarjetas (Idéntico a tu dashboard.php)
    foreach ($resultados as $juegoDatos) {
        $juego_user_id = $juegoDatos['user_id'];
        $juegoID = $juegoDatos['id'];
        $juegoNombre = htmlspecialchars($juegoDatos['titulo']);
        $juegoAutor = htmlspecialchars($juegoDatos['autor']);
        $juegoDescripcion = htmlspecialchars($juegoDatos['descripcion']);
        $juegoCaratulaFoto = htmlspecialchars($juegoDatos['caratula_path']);
        $juegoCategoria = htmlspecialchars($juegoDatos['categoria']);
        $juegoUrl = htmlspecialchars($juegoDatos['url']);
        $juegoYear = htmlspecialchars($juegoDatos['year_juego']);

        // Botones de editar/borrar solo si es el dueño
        $botonesAdmin = '';
        if ($user_id == $juego_user_id) {
            $botonesAdmin = "
                <div class='ms-auto'>
                    <a href='editarJuego.php?id=$juegoID' class='btn btn-sm btn-warning me-2'>Editar</a>
                    <a href='eliminarJuego.php?id=$juegoID' class='btn btn-sm btn-danger'>Eliminar</a>
                </div>
            ";
        }

        echo "
        <div class='col'>
            <div class='card h-100 shadow-sm border-0'>
                <img src='$juegoCaratulaFoto' alt='Carátula del juego' class='card-img-top game-cover'> 
                <div class='card-body'>
                    <h5 class='card-title text-primary'><a href='detalleJuego.php?id=$juegoID' class='text-decoration-none'>$juegoNombre</a></h5>
                    <h6 class='card-subtitle mb-2 text-muted'>$juegoAutor ($juegoYear)</h6>
                    <p class='card-text text-truncate'>$juegoDescripcion</p>
                    <span class='badge bg-secondary'>$juegoCategoria</span>
                </div>
                <div class='card-footer bg-transparent border-0 d-flex justify-content-between'>
                    <a href='$juegoUrl' class='btn btn-sm btn-outline-dark' target='_blank'>Visitar URL</a>
                    $botonesAdmin
                </div>
            </div>
        </div>
        ";
    }

} catch (PDOException $e) {
    echo "Error en la búsqueda.";
}
?>