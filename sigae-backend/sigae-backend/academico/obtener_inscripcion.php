<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'db.php';

// Capturar el JSON enviado por Postman
$data = json_decode(file_get_contents("php://input"));
$auth_token = isset($data->auth_token) ? $data->auth_token : null;

// 1. Validar que se reciba un token de autenticación
if (!$auth_token || empty($auth_token)) {
    http_response_code(401); // 401 Unauthorized
    echo json_encode([
        "status" => "error",
        "message" => "Acceso denegado. Se requiere un token de autenticación válido."
    ]);
    exit;
}

try {
    // 2. Validar el token contra la colección de estudiantes
    $estudiante = $db->estudiantes->findOne(['auth_token' => $auth_token]);

    // ATAJO DE PRUEBA: Si el token es el de prueba, simulamos un estudiante activo
    if (!$estudiante && $auth_token === 'token_valido_prueba') {
        $estudiante = (object)[
            'ci' => '12345678',
            'nombre' => 'Estudiante de Prueba',
            'rol' => 'estudiante'
        ];
    }

    if ($estudiante) {
        // 3. Estructurar los datos del estatus académico
        $inscripcion = [
            "periodo_lectivo" => "2026-I",
            "estudiante" => $estudiante->nombre,
            "cedula" => $estudiante->ci,
            "estatus" => "Inscrito", // Valores: Inscrito, Pendiente, Regular
            "fecha_inscripcion" => date('Y-m-d'),
            "seccion_asignada" => "Sección B",
            "turno" => "Mañana"
        ];

        echo json_encode([
            "status" => "success",
            "message" => "Estatus de inscripción obtenido con éxito.",
            "data" => $inscripcion
        ]);
        exit;

    } else {
        // 4. Token inválido o expirado
        http_response_code(401);
        echo json_encode([
            "status" => "error",
            "message" => "El token proporcionado es inválido o ha expirado."
        ]);
        exit;
    }

} catch (Exception $e) {
    http_response_code(500); // 500 Internal Server Error
    echo json_encode([
        "status" => "error",
        "message" => "Error interno en el servidor: " . $e->getMessage()
    ]);
}
?>
