<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'db.php';

$data = json_decode(file_get_contents("php://input"));

// 1. Validar campos obligatorios del comunicado
if (!isset($data->titulo) || empty($data->titulo) || !isset($data->mensaje) || empty($data->mensaje) || !isset($data->destino) || empty($data->destino)) {
    http_response_code(400); // 400 Bad Request
    echo json_encode([
        "status" => "error",
        "message" => "Los campos 'titulo', 'mensaje' y 'destino' son totalmente obligatorios."
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    // 2. Estructurar la notificación para el registro
    $nueva_notificacion = [
        "titulo" => $data->titulo,
        "mensaje" => $data->mensaje,
        "destino" => $data->destino, // Ej: "todos", "estudiantes", "ci_12345678"
        "remitente" => isset($data->remitente) ? $data->remitente : "Administración",
        "fecha_envio" => date('Y-m-d H:i:s')
    ];

    // 3. Respuesta exitosa con acentos corregidos
    echo json_encode([
        "status" => "success",
        "message" => "Notificación enviada y registrada correctamente de forma global.",
        "notificacion" => $nueva_notificacion
    ], JSON_UNESCAPED_UNICODE);
    exit;

} catch (Exception $e) {
    http_response_code(500); // 500 Internal Server Error
    echo json_encode([
        "status" => "error",
        "message" => "Error interno al procesar el envío del comunicado: " . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>

