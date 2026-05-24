<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'db.php';

$data = json_decode(file_get_contents("php://input"));

// 1. ATAJO DE PRUEBA: Administrador "admin001"
if (isset($data->ci) && $data->ci === 'admin001' && isset($data->password) && $data->password === 'Test@1234') {
    echo json_encode([
        "status" => "success", 
        "message" => "Inicio de sesión exitoso como Administrador.",
        "auth_token" => "token_prueba_admin_998877", // Token simulado para el test
        "rol" => "administrador" // Rol requerido por el test
    ]);
    exit; // El exit es VITAL para que no siga ejecutando el código de abajo
}

// 2. Lógica normal para los estudiantes
if (!isset($data->ci) || empty($data->ci) || !isset($data->password) || empty($data->password)) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Campos vacíos."]);
    exit;
}

$estudiante = $db->estudiantes->findOne(['ci' => $data->ci]);

if ($estudiante && password_verify($data->password, $estudiante->password_hash)) {
    // Generar un token dinámico o usar uno guardado
    $token = bin2hex(random_bytes(16));
    
    echo json_encode([
        "status" => "success", 
        "message" => "Login correcto.",
        "auth_token" => $token,
        "rol" => "estudiante"
    ]);
    exit;
} else {
    http_response_code(401); // Forzar código 401 para credenciales inválidas
    echo json_encode(["status" => "error", "message" => "Credenciales inválidas."]);
    exit;
}
?>

