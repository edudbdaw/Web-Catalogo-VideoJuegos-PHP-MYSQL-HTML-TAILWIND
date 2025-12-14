# 🎮 Catálogo de Videojuegos

Proyecto para la asignatura de **Desarrollo Web en Entorno Servidor**. Es una aplicación web completa para gestionar una biblioteca personal de videojuegos, permitiendo subir, editar, valorar y buscar títulos.

🔗 **[Ver Proyecto Online](https://eduardoduran.infinityfree.me/dashboard.php)**

## 🚀 Tecnologías Utilizadas

* **Lenguaje:** PHP (Nativo, sin frameworks).
* **Base de Datos:** MySQL (PDO).
* **Frontend:** HTML5 y **Bootstrap 5** (para diseño responsive).
* **Interacción:** JavaScript (AJAX) para búsqueda en tiempo real y votos.

## ✨ Funcionalidades Principales

1.  **Gestión de Usuarios:**
    * Registro y Login seguro (contraseñas hasheadas).
    * Subida de foto de perfil personalizada.
    * Gestión de sesiones.
2.  **CRUD de Videojuegos:**
    * Crear, Leer, Actualizar y Borrar juegos.
    * Subida de imágenes (carátulas) al servidor.
3.  **Interacción:**
    * **Buscador en vivo:** Filtra por título o autor sin recargar la página (AJAX).
    * **Sistema de Votos:** "Me gusta" / "No me gusta" con conteo dinámico.
    * Contador de visualizaciones por juego.
4.  **Panel de Control:**
    * Vista de estadísticas de mis juegos subidos.

## 📦 Instalación Local

Si quieres probar este proyecto en tu ordenador (XAMPP/WAMP):

1.  Clona este repositorio.
2.  Importa el archivo `.sql` (base de datos) en phpMyAdmin.
3.  Configura el archivo `connbbdd.php` con tus credenciales:
    ```php
    $db_server = 'localhost';
    $db_name = 'nombre_de_tu_bd';
    $db_user = 'root';
    $db_password = '';
    ```
4.  Abre el navegador en `localhost/tu-carpeta/form_login.php`.

---
*Desarrollado por Eduardo Durán.*
