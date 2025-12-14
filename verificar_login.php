<?php
if (isset($_POST['username'])) {
    $username = $_POST['username'];
    if (empty($username)) {
        echo "<span class='error'>Debe introducir un usuario</span>";
    } elseif (strlen($username) < 6) {
        echo "<span class='error'>El usuario debe tener al menos 6 caracteres</span>";
    } else {
        echo "<span class='ok'>Usuario válido</span>";
    }
}

if (isset($_POST['password'])) {
    $password = $_POST['password'];
    $patron = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()\-_=+{};:,<.>]).{8,20}$/';
    if (empty($password)) {
        echo "<span class='error'>Debe introducir una contraseña</span>";
    } elseif (!preg_match($patron, $password)) {
        echo "<span class='error'>La contraseña debe ser de 8 caracteres minimo , contener un simbolo , mayusculas , minusculas y numeros</span>";
    } else {
        echo "<span class='ok'>Contraseña válida</span>";
    }
}