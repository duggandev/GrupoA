<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(['error' => 'Método no permitido. Usa POST.'], 405);
}

$input = file_get_contents('php://input');
$request = json_decode($input ?: '', true);

if (!is_array($request)) {
    respond(['error' => 'El cuerpo debe ser un JSON válido.'], 400);
}

$requiredFields = [
    'id_restaurante',
    'fecha',
    'hora',
    'numero_de_comensales',
    'id_zona',
];

foreach ($requiredFields as $field) {
    if (!array_key_exists($field, $request)) {
        respond(['error' => "Falta el campo requerido: {$field}."], 400);
    }
}

if (!is_string($request['id_restaurante']) || $request['id_restaurante'] === ''
    || !is_string($request['fecha']) || !is_string($request['hora'])
    || !is_string($request['id_zona']) || $request['id_zona'] === ''
    || !is_int($request['numero_de_comensales'])
    || $request['numero_de_comensales'] < 1
) {
    respond(['error' => 'Los datos no cumplen el formato esperado.'], 400);
}

$dataFile = __DIR__ . '/data/restaurants.json';
$data = json_decode((string) file_get_contents($dataFile), true);

if (!is_array($data)) {
    respond(['error' => 'No se pudo cargar la fuente de datos.'], 500);
}

$restaurant = $data['restaurantes'][$request['id_restaurante']] ?? null;
$zone = $restaurant['zonas'][$request['id_zona']] ?? null;

$available = is_array($restaurant)
    && ($restaurant['activo'] ?? false) === true
    && is_array($zone)
    && $request['numero_de_comensales'] <= ($zone['capacidad_maxima'] ?? 0)
    && in_array($request['fecha'], $zone['fechas_disponibles'] ?? [], true)
    && in_array($request['hora'], $zone['horas_disponibles'] ?? [], true);

respond(['disponible' => $available]);

function respond(array $payload, int $status = 200): never
{
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    exit;
}
