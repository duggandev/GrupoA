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

foreach ($restaurantes as $r) {
    if ($r['id_restaurante'] == $data['id_restaurante']) {
        $existe = true;
        break;
    }
}

if (!$existe) {
    http_response_code(404);
    echo json_encode(['disponible' => false, 'error' => 'Restaurante no existe']);
    exit;
}

http_response_code(200);
echo json_encode(['disponible' => true]);
