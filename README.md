# Calcular Tarifa de Envío

Este proyecto es una API sencilla en PHP que calcula la tarifa de envío basada en la distancia.

## Uso

1. Realiza una solicitud `POST` al archivo `Index.php`.
2. Envía un cuerpo JSON con los siguientes parámetros:
   - `origen` (string): Lugar de origen.
   - `destino` (string): Lugar de destino.
   - `distancia` (número): Distancia en kilómetros.

### Ejemplo de solicitud

```json
{
  "origen": "Ciudad A",
  "destino": "Ciudad B",
  "distancia": 100
}
```

Respuesta exitosa:

```json
{
  "origen": "Ciudad A",
  "destino": "Ciudad B",
  "distancia": 100,
  "tarifa_envio": 500
}
```

Respuesta con parámetros inválidos:

```json
{
  "error": "Parámetros inválidos. Se requieren 'origen', 'destino' y 'distancia' (numérico)."
}
```

Respuesta para otro método HTTP:

```json
{
  "error": "Método no permitido. Use POST."
}
```