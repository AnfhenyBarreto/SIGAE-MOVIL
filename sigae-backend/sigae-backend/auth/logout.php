<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'db.php';

// Capturar el JSON enviado por Postman
$data = json_decode(file_get_contents("php://input"));

// 1. Validar que se haya enviado el token para poder cerrar la sesión
if (!isset($data->auth_token) || empty($data->auth_token)) {
    http_response_code(400); // 400 Bad Request: Petición incorrecta
    echo json_encode([
        "status" => "error", 
        "message" => "No se proporcionó un token para cerrar sesión."
    ]);
    exit;
}

try {
    // 2. Buscar si el token existe en la colección de estudiantes
    $estudiante = $db->estudiantes->findOne(['auth_token' => $data->auth_token]);

    if ($estudiante) {
        // 3. Invalidar el token borrándolo del documento del estudiante (setear a null)
        $db->estudiantes->updateOne(
            ['_id' => $estudiante->_id],
            ['$set' => ['auth_token' => null]]
        );

        echo json_encode([
            "status" => "success", 
            "message" => "Sesión cerrada correctamente. Token invalidado."
        ]);
    } else {
        // 4. Si el token enviado no existe en la base de datos
        http_response_code(401); // 401 Unauthorized
        echo json_encode([
            "status" => "error", 
            "message" => "El token proporcionado no es válido o ya fue invalidado."
        ]);
    }

} catch (Exception $e) {
    http_response_code(500); // 500 Internal Server Error
    echo json_encode([
        "status" => "error", 
        "message" => "Error interno al procesar el cierre de sesión: " . $e->getMessage()
    ]);
}
?>
