<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Método no permitido. Use POST."]);
    exit;
}

try {
    $input = json_decode(file_get_contents("php://input"), true, 512, JSON_THROW_ON_ERROR);
} catch (JsonException) {
    http_response_code(400);
    echo json_encode(["error" => "El cuerpo debe ser un JSON válido."]);
    exit;
}

if (
    is_array($input)
    && isset($input['origen'], $input['destino'], $input['distancia'])
    && is_string($input['origen'])
    && is_string($input['destino'])
    && trim($input['origen']) !== ''
    && trim($input['destino']) !== ''
    && is_numeric($input['distancia'])
    && is_finite((float) $input['distancia'])
    && (float) $input['distancia'] >= 0
) {
    $origen = trim($input['origen']);
    $destino = trim($input['destino']);
    $distancia = (float) $input['distancia'];

    $tarifa_por_km = 5;
    $tarifa_envio = $distancia * $tarifa_por_km;

    echo json_encode([
        "origen" => $origen,
        "destino" => $destino,
        "distancia" => $distancia,
        "tarifa_envio" => $tarifa_envio
    ]);
} else {
    http_response_code(400);
    echo json_encode(["error" => "Parámetros inválidos. Se requieren 'origen', 'destino' y 'distancia' (numérico no negativo)."]);
}