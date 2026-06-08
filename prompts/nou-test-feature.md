# Plantilla: Nou test Feature PHPUnit

> Copia, substitueix `{{...}}` i envia.

---

## Context i Rol

Ets un desenvolupador PHP/Laravel expert en el projecte intranetBatoi.
Namespace arrel: `Intranet\`. Tests a `tests/Feature/`.
Llegeix `AGENTS.md` i `docs/agents/testing-docker.md` abans de generar res.

## Tasca

Crea un test Feature per a **{{descripció del comportament a testar}}**.

## Especificacions

- Controlador/ruta a testar: `{{prefix/nom-ruta}}`
- Rol d'usuari que fa la petició: `{{profesor | alumno | direccion | …}}`
- Mètode HTTP: `{{GET | POST | PUT | DELETE}}`
- Resposta esperada en cas OK: `{{codi HTTP + contingut/redirect}}`
- Resposta esperada sense permís: `{{403 | redirect login | …}}`
- Efectes col·laterals a verificar: `{{DB, events, correus, …}}`
- Factory/Seeder necessari: `{{NomFactory o "usar el seeder existent"}}`

## Criteris de qualitat

- Usa `RefreshDatabase`.
- Un mètode de test per a cada escenari (OK, sense autenticació, sense permís, validació fallida).
- Noms de mètode en format `test_{{descripcio_snake_case}}`.
- Cap lògica de negoci en el test: usa factories i helpers del projecte.
- El test ha de passar en entorn Docker (`docker compose exec -T laravel.test php artisan test --filter={{NomTest}}`).

## Format de resposta

1. Fitxer `tests/Feature/{{NomTest}}.php` complet.
2. Si cal un factory nou: `database/factories/{{NomEntitat}}Factory.php`.
3. Comanda per executar-lo en local i en Docker.
