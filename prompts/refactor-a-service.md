# Plantilla: Extreure lògica a Application/Service

> Copia, substitueix `{{...}}` i envia.

---

## Context i Rol

Ets un desenvolupador PHP/Laravel expert en el projecte intranetBatoi.
Capes: `app/Http/Controllers/` (entrada HTTP), `app/Application/` (casos d'ús), `app/Domain/` (interfícies).
Llegeix `AGENTS.md` i `docs/agents/conventions.md` abans de generar res.

## Tasca

Extreu la lògica de negoci del mètode `{{NomControlador::nomMètode()}}` cap a un servei a `app/Application/{{NomDomini}}/`.

## Especificacions

- Fitxer origen: `app/Http/Controllers/{{Rol}}/{{NomControlador}}.php`, mètode `{{nomMètode}}`
- Servei destí: `app/Application/{{NomDomini}}/{{NomServei}}.php`
- Mètode nou al servei: `{{nomMètodeServei(params)}}`
- El controlador ha de quedar com a punt d'entrada HTTP pur (rep request, delega, retorna resposta).
- Injecció de dependències: `{{llista de repositoris/serveis que necessita}}`

## Criteris de qualitat

- El controlador no ha de contindre lògica de negoci després del refactor.
- El servei ha de ser testable de forma aïllada (sense HttpRequest).
- PHPDoc al servei i al mètode nou.
- No modificar el comportament observable (mateixa resposta HTTP, mateixos efectes BD).
- Si hi havia un test existent, ha de continuar passant.

## Format de resposta

1. `app/Application/{{NomDomini}}/{{NomServei}}.php` (servei nou o actualitzat).
2. `app/Http/Controllers/{{Rol}}/{{NomControlador}}.php` (controlador refactoritzat).
3. Confirmació que els tests existents continuen passant (`composer test:quick` o equivalent).
