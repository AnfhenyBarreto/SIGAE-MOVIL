<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'db.php';

try {
    // Encriptamos la clave del administrador
    $password_encriptada = password_hash("Test@1234", PASSWORD_BCRYPT);

    $nuevoAdmin = [
        "ci" => "admin001", // Tu admin_cedula de Postman
        "password_hash" => $password_encriptada,
        "nombre" => "Administrador General",
        "rol" => "admin"
    ];

    // Se inserta en la colección 'estudiantes' que lee tu archivo login.php
    $resultado = $db->estudiantes->insertOne($nuevoAdmin);

    echo json_encode([
        "status" => "success",
        "message" => "¡Administrador de prueba creado con éxito localmente!",
        "id" => (string)$resultado->getInsertedId()
    ]);

} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => "No se pudo crear el administrador: " . $e->getMessage()
    ]);
}
?>

