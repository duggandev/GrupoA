<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['id_restaurante'], $data['fecha'], $data['hora'], $data['numero_de_comensales'], $data['id_zona'])) {
    http_response_code(400);
    echo json_encode(['disponible' => false, 'error' => 'Falta campo requerido']);
    exit;
}

$restaurantes = json_decode(file_get_contents(__DIR__ . '/../data/restaurantes.json'), true);
$existe = false;

$existe = false;

foreach ($restaurantes as $r) {
    // cambios 
    // Validamos que coincidan TODOS los parámetros enviados por Postman, si un parametro es diferente al que está en json saldrá error
    if (
        $r['id_restaurante'] == $data['id_restaurante'] &&
        $r['fecha'] == $data['fecha'] &&
        $r['hora'] == $data['hora'] &&
        $r['id_zona'] == $data['id_zona']
    ) {
        // Si coincide todo, evaluamos si hay espacio suficiente :D
        if ($data['numero_de_comensales'] <= $r['numero_de_comensales']) {
            $existe = true;
            break;
        }
    }
}

if (!$existe) {
    http_response_code(404);
    echo json_encode(['disponible' => false, 'error' => 'Restaurante no existe']);
    exit;
}

http_response_code(200);
echo json_encode(['disponible' => true]);
