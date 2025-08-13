<?php
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['password']) || !isset($data['deviceId'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'msg' => 'Datos incompletos']);
    exit;
}

$password = $data['password'];
$deviceId = $data['deviceId'];
$archivo = __DIR__ . '/../private/contraseñas.json';

if (!file_exists($archivo)) {
    file_put_contents($archivo, json_encode(new stdClass()));
}

$contraseñas = json_decode(file_get_contents($archivo), true);

// Verificar que exista la contraseña
if (!isset($contraseñas[$password])) {
    http_response_code(404);
    echo json_encode(['status' => 'error', 'msg' => 'Contraseña incorrecta']);
    exit;
}

// Verificar si la contraseña venció
$fecha_expira = strtotime($contraseñas[$password]['expira']);
if ($fecha_expira < time()) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'msg' => 'Contraseña vencida']);
    exit;
}

// Verificar si ya está asignada a otro dispositivo
if (!empty($contraseñas[$password]['dispositivo']) && $contraseñas[$password]['dispositivo'] !== $deviceId) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'msg' => 'Contraseña ya en uso en otro dispositivo']);
    exit;
}

// Asignar dispositivo si está vacío
if (empty($contraseñas[$password]['dispositivo'])) {
    $contraseñas[$password]['dispositivo'] = $deviceId;
    file_put_contents($archivo, json_encode($contraseñas, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

echo json_encode(['status' => 'ok', 'msg' => 'Acceso permitido']);
?>




