<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

function cargarRestaurantes(): array
{
    $ruta = __DIR__ . '/../data/restaurantes.json';
    $contenido = file_get_contents($ruta);

    if ($contenido === false) {
        return [];
    }

    $datos = json_decode($contenido, true);
    return is_array($datos) ? $datos : [];
}

function obtenerEntrada(): array
{
    $entradas = json_decode(file_get_contents('php://input'), true);

    if (!is_array($entradas) || count($entradas) === 0) {
        $entradas = $_GET;
    }

    return is_array($entradas) ? $entradas : [];
}

function validarFecha(string $fecha): bool
{
    $date = DateTimeImmutable::createFromFormat('!Y-m-d', $fecha);
    return $date !== false && $date->format('Y-m-d') === $fecha;
}

function validarHora(string $hora): bool
{
    $time = DateTimeImmutable::createFromFormat('!H:i', $hora);
    return $time !== false && $time->format('H:i') === $hora;
}

function hayConflictoHorario(string $horaSolicitada, string $apertura, string $cierre): bool
{
    $solicitada = strtotime($horaSolicitada);
    $inicio = strtotime($apertura);
    $fin = strtotime($cierre);

    if ($inicio === false || $fin === false || $solicitada === false) {
        return true;
    }

    if ($fin < $inicio) {
        $fin += 24 * 60 * 60;
        if ($solicitada < $inicio) {
            $solicitada += 24 * 60 * 60;
        }
    }

    return !($solicitada >= $inicio && $solicitada <= $fin);
}

function responderJson(array $payload, int $codigo): void
{
    http_response_code($codigo);
    echo json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

$entrada = obtenerEntrada();
$camposRequeridos = ['id_restaurante', 'fecha', 'hora', 'numero_de_comensales', 'id_zona'];

foreach ($camposRequeridos as $campo) {
    if (!array_key_exists($campo, $entrada)) {
        responderJson([
            'disponible' => false,
            'error' => "Falta el campo requerido: {$campo}"
        ], 400);
    }
}

$idRestaurante = (int) $entrada['id_restaurante'];
$fecha = trim((string) $entrada['fecha']);
$hora = trim((string) $entrada['hora']);
$numeroComensales = (int) $entrada['numero_de_comensales'];
$idZona = (int) $entrada['id_zona'];

if (!validarFecha($fecha) || !validarHora($hora) || $numeroComensales <= 0) {
    responderJson([
        'disponible' => false,
        'error' => 'La fecha, la hora o la cantidad de comensales no son válidos.'
    ], 400);
}

$restaurantes = cargarRestaurantes();
$restaurante = null;

foreach ($restaurantes as $item) {
    if ((int) $item['id_restaurante'] === $idRestaurante) {
        $restaurante = $item;
        break;
    }
}

if ($restaurante === null) {
    responderJson([
        'disponible' => false,
        'error' => 'No existe un restaurante con ese id_restaurante.'
    ], 404);
}

if ((int) $restaurante['id_zona'] !== $idZona) {
    responderJson([
        'disponible' => false,
        'error' => 'La zona solicitada no coincide con el restaurante.'
    ], 409);
}

if ($numeroComensales > (int) $restaurante['capacidad']) {
    responderJson([
        'disponible' => false,
        'error' => 'El restaurante no tiene capacidad suficiente para ese número de comensales.'
    ], 409);
}

if (in_array($fecha, $restaurante['dias_cerrados'] ?? [], true)) {
    responderJson([
        'disponible' => false,
        'error' => 'El restaurante está cerrado en la fecha solicitada.'
    ], 409);
}

$horario = $restaurante['horario'] ?? [];
if (isset($horario['apertura'], $horario['cierre']) && hayConflictoHorario($hora, (string) $horario['apertura'], (string) $horario['cierre'])) {
    responderJson([
        'disponible' => false,
        'error' => 'El restaurante no atiende en la hora solicitada.'
    ], 409);
}

foreach ($restaurante['reservas'] ?? [] as $reserva) {
    if (($reserva['fecha'] ?? '') === $fecha && ($reserva['hora'] ?? '') === $hora) {
        $totalReservado = (int) ($reserva['comensales'] ?? 0);
        if ($totalReservado + $numeroComensales > (int) $restaurante['capacidad']) {
            responderJson([
                'disponible' => false,
                'error' => 'No hay disponibilidad en ese horario.'
            ], 409);
        }
    }
}

responderJson([
    'disponible' => true
], 200);
