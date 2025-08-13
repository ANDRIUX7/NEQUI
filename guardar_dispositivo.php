<?php
// Recibe JSON desde fetch
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['password']) || !isset($data['deviceId'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'msg' => 'Datos incompletos']);
    exit;
}

$password = $data['password'];
$deviceId = $data['deviceId'];

// Ruta al JSON de contraseñas
$archivo = 'contraseñas.json';

// Leer el JSON
if (!file_exists($archivo)) {
    file_put_contents($archivo, json_encode(new stdClass()));
}

$contraseñas = json_decode(file_get_contents($archivo), true);

// Verificar que exista la contraseña
if (!isset($contraseñas[$password])) {
    http_response_code(404);
    echo json_encode(['status' => 'error', 'msg' => 'Contraseña no encontrada']);
    exit;
}

// Asignar dispositivo si está vacío
if (empty($contraseñas[$password]['dispositivo'])) {
    $contraseñas[$password]['dispositivo'] = $deviceId;

    file_put_contents($archivo, json_encode($contraseñas, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    echo json_encode(['status' => 'ok', 'msg' => 'Dispositivo asignado']);
} else {
    echo json_encode(['status' => 'ok', 'msg' => 'Dispositivo ya asignado']);
}
?>
