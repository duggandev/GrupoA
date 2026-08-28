# Servicio PHP: ValidarDisponibilidadRestaurante

Este proyecto implementa un endpoint PHP real para simular la disponibilidad de un restaurante sin usar base de datos.

## Archivos

- `api/validarDisponibilidadRestaurante.php`: endpoint principal.
- `data/restaurantes.json`: datos simulados del restaurante.

## Endpoint

### Método

- `GET` o `POST`

### Entrada esperada

```json
{
  "id_restaurante": 101,
  "fecha": "2026-08-28",
  "hora": "20:00",
  "numero_de_comensales": 10,
  "id_zona": 1
}
```

### Respuesta exitosa

```json
{
  "disponible": true
}
```

### Ejemplo de uso con curl

```bash
php -S localhost:8000 -t .
curl -X POST http://localhost:8000/api/validarDisponibilidadRestaurante.php \
  -H "Content-Type: application/json" \
  -d '{"id_restaurante":101,"fecha":"2026-08-28","hora":"20:00","numero_de_comensales":10,"id_zona":1}'
```

## Nota

El servicio usa un archivo JSON como fuente de datos simulada, cumpliendo con el requisito de no depender de un gestor de base de datos.
