<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'db.php';

$data = json_decode(file_get_contents("php://input"));

// 1. Validar campos obligatorios de la sugerencia
if (!isset($data->tipo) || empty($data->tipo) || !isset($data->contenido) || empty($data->contenido)) {
    http_response_code(400); // 400 Bad Request
    echo json_encode([
        "status" => "error",
        "message" => "Los campos 'tipo' (queja, sugerencia, felicitación) y 'contenido' son obligatorios."
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    // 2. Estructurar la sugerencia para la base de datos
    // Si no se envía cédula o nombre, se marca automáticamente como 'Anónimo'
    $nueva_sugerencia = [
        "tipo" => $data->tipo, // Ejemplo: "Sugerencia", "Queja", "Reclamo"
        "contenido" => $data->contenido,
        "usuario_ci" => isset($data->usuario_ci) && !empty($data->usuario_ci) ? $data->usuario_ci : "Anónimo",
        "fecha_registro" => date('Y-m-d H:i:s'),
        "estatus" => "Pendiente Por Revisar"
    ];

    // Simulamos éxito de guardado en MongoDB
    echo json_encode([
        "status" => "success",
        "message" => "Su sugerencia ha sido recibida de forma exitosa. Gracias por ayudarnos a mejorar.",
        "sugerencia" => $nueva_sugerencia
    ], JSON_UNESCAPED_UNICODE);
    exit;

} catch (Exception $e) {
    http_response_code(500); // 500 Internal Server Error
    echo json_encode([
        "status" => "error",
        "message" => "Error interno al procesar el buzón de sugerencias: " . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>
