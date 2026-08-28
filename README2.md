# ValidarDisponibilidadRestaurante

Endpoint PHP que implementa el contrato del servicio de disponibilidad sin gestor de base de datos.

## Ejecutar

Desde esta carpeta:

```powershell
php -S localhost:8000
```

## Contrato

`POST /index.php`

Request JSON:

```json
{
  "id_restaurante": "rest-001",
  "fecha": "2026-09-01",
  "hora": "20:00",
  "numero_de_comensales": 4,
  "id_zona": "terraza"
}
```

Respuesta exitosa:

```json
{
  "disponible": true
}
```

La disponibilidad se calcula consultando `data/restaurants.json`: restaurante activo, zona existente, capacidad suficiente, fecha y hora habilitadas.

## Probar

```powershell
Invoke-RestMethod `
  -Uri http://localhost:8000/index.php `
  -Method Post `
  -ContentType 'application/json' `
  -Body '{"id_restaurante":"rest-001","fecha":"2026-09-01","hora":"20:00","numero_de_comensales":4,"id_zona":"terraza"}'
```
