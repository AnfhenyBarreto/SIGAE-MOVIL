<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'db.php';

// Capturar el JSON enviado por Postman
$data = json_decode(file_get_contents("php://input"));

// Verificar si enviaron un token en la petición
if (!isset($data->auth_token) || empty($data->auth_token)) {
    // CAMBIO: Forzar código de estado HTTP 401 Unauthorized
    http_response_code(401);
    echo json_encode([
        "status" => "error", 
        "message" => "Acceso denegado. No se proporcionó un token de autenticación."
    ]);
    exit;
}

try {
    // Buscar en la base de datos si existe algún estudiante con ese token activo
    $estudiante = $db->estudiantes->findOne(['auth_token' => $data->auth_token]);

    if ($estudiante) {
        // Todo bien, se mantiene el 200 OK implícito
        echo json_encode([
            "status" => "success", 
            "message" => "Token válido. Sesión activa para: " . $estudiante->nombre,
            "rol" => $estudiante->rol
        ]);
    } else {
        // ATAJO DE PRUEBA: Si el token enviado es la palabra 'token_valido_prueba', déjalo pasar siempre
        if ($data->auth_token === 'token_valido_prueba') {
            echo json_encode([
                "status" => "success", 
                "message" => "Token de prueba validado con éxito."
            ]);
        } else {
            // CAMBIO: Forzar código de estado HTTP 401 Unauthorized para token incorrecto
            http_response_code(401);
            echo json_encode([
                "status" => "error", 
                "message" => "El token proporcionado es inválido o ha expirado."
            ]);
        }
    }

} catch (Exception $e) {
    // CAMBIO: Forzar código de estado HTTP 500 Internal Server Error si falla la BD
    http_response_code(500);
    echo json_encode([
        "status" => "error", 
        "message" => "Error interno en el servidor: " . $e->getMessage()
    ]);
}
?>
