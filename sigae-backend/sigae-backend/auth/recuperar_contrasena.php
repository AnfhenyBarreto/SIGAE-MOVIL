<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'db.php';

// Capturar el JSON enviado por Postman
$data = json_decode(file_get_contents("php://input"));

// 1. Validar que se hayan proporcionado la cédula y el rol
if (!isset($data->cedula) || empty($data->cedula) || !isset($data->role) || empty($data->role)) {
    http_response_code(400); // 400 Bad Request
    echo json_encode([
        "status" => "error", 
        "message" => "Los campos de cédula y rol son obligatorios."
    ]);
    exit;
}

try {
    // 2. Buscar en la base de datos por cédula y rol (mapeando 'role' a tu campo 'rol')
    // Nota: Ajusta 'rol' si en tu base de datos el campo se llama exactamente 'role'
    $estudiante = $db->estudiantes->findOne([
        'cedula' => $data->cedula,
        'rol' => $data->role 
    ]);

    if ($estudiante) {
        // 3. Generar un token único y seguro para la recuperación
        $reset_token = bin2hex(random_bytes(32));
        $expiracion = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // 4. Guardar el token de recuperación en el documento del estudiante
        $db->estudiantes->updateOne(
            ['_id' => $estudiante->_id],
            ['$set' => [
                'reset_token' => $reset_token,
                'reset_token_expira' => $expiracion
            ]]
        );

        echo json_encode([
            "status" => "success", 
            "message" => "Solicitud procesada. Se ha generado un token de recuperación para " . $estudiante->nombre,
            "debug_token" => $reset_token // Para facilitar tus pruebas en Postman
        ]);
        
    } else {
        // 5. Si la combinación de cédula y rol no existe
        http_response_code(404); // 404 Not Found
        echo json_encode([
            "status" => "error", 
            "message" => "No se encontró ningún usuario registrado con esos datos."
        ]);
    }

} catch (Exception $e) {
    http_response_code(500); // 500 Internal Server Error
    echo json_encode([
        "status" => "error", 
        "message" => "Error interno al procesar la solicitud: " . $e->getMessage()
    ]);
}
?>

