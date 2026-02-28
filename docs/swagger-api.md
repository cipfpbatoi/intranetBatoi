# Swagger / OpenAPI

Aquesta guia explica com generar i consultar la documentacio de l'API.

## Dependencia

El projecte usa `darkaonline/l5-swagger` per generar la UI de Swagger i l'especificacio OpenAPI.

## Generar documentacio

```bash
php artisan l5-swagger:generate
```

## Obrir la UI Swagger

Amb el servidor actiu, obri:

- `/api/documentation`

Per exemple en local:

- `http://localhost/api/documentation`

## Autenticacio

S'ha definit l'esquema `Bearer` (`sanctum`).

1. Crida `POST /api/auth/exchange` per obtindre `access_token`.
2. En Swagger, polsa `Authorize` i pega: `Bearer <token>`.
3. Prova endpoints protegits com `GET /api/auth/me`.

### Flux rapid de prova

1. Executa `POST /api/auth/exchange` amb:

```json
{
  "api_token": "EL_TEU_TOKEN_LEGACY",
  "device_name": "swagger-ui",
  "dni": "12345678A"
}
```

2. Copia `data.access_token` de la resposta.
3. En Swagger, prem `Authorize` i posa `Bearer <access_token>`.
4. Prova `GET /api/auth/me` per validar el token.

## Classificacio d endpoints

Els endpoints estan classificats per `tags` amb el format:

- públics: `<Modul> (Public)`
- resta: `<Modul>`

Exemples:

- `FCT (Public)`
- `Materials`
- `Guardies (Public)`
- `Auth` i `Auth (Public)`

En Swagger UI pots usar el filtre de tags per veure sols una area funcional.

## Notes

- Si canvies anotacions, torna a executar `php artisan l5-swagger:generate`.
- En produccio, mantin `L5_SWAGGER_GENERATE_ALWAYS=false`.
