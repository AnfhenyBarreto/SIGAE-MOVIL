<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'db.php';

// Capturar los encabezados (Headers) para obtener el Token
$headers = getallheaders();
$auth_token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;

// Si no viene en los Headers, verificar si lo enviaron en el JSON del Body
if (!$auth_token) {
    $data = json_decode(file_get_contents("php://input"));
    $auth_token = isset($data->auth_token) ? $data->auth_token : null;
}

// 1. Validar que se reciba un token de autenticación
if (!$auth_token || empty($auth_token)) {
    http_response_code(401); // 401 Unauthorized
    echo json_encode([
        "status" => "error",
        "message" => "Acceso denegado. Se requiere un token válido para consultar las notas."
    ]);
    exit;
}

try {
    // 2. Validar el token contra la colección de estudiantes
    $estudiante = $db->estudiantes->findOne(['auth_token' => $auth_token]);

    // ATAJO DE PRUEBA: Si el token es el de prueba, simulamos un estudiante
    if (!$estudiante && $auth_token === 'token_valido_prueba') {
        $estudiante = (object)[
            'ci' => '12345678',
            'nombre' => 'Estudiante de Prueba',
            'rol' => 'estudiante'
        ];
    }

    if ($estudiante) {
        // 3. Buscar las notas asociadas al estudiante
        // Edgar (Base de Datos) definirá la colección final; aquí simulamos una búsqueda o respuesta estructurada
        $boletin = [
            "periodo" => "2026-I",
            "estudiante" => $estudiante->nombre,
            "cedula" => $estudiante->ci,
            "materias" => [
                ["codigo" => "MAT-101", "nombre" => "Matemáticas I", "nota" => 18, "estado" => "Aprobado"],
                ["codigo" => "PROG-102", "nombre" => "Programación Web PHP", "nota" => 20, "estado" => "Aprobado"],
                ["codigo" => "BD-103", "nombre" => "Bases de Datos NoSQL", "nota" => 16, "estado" => "Aprobado"],
                ["codigo" => "ING-104", "nombre" => "Inglés Técnico", "nota" => 12, "estado" => "Aprobado"]
            ],
            "promedio" => 16.5
        ];

        echo json_encode([
            "status" => "success",
            "message" => "Boletín académico obtenido con éxito.",
            "data" => $boletin
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
        "message" => "Error interno al consultar las calificaciones: " . $e->getMessage()
    ]);
}
?>
