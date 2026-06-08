# Plantilla: Nova entitat Eloquent

> Copia, substitueix `{{...}}` i envia.

---

## Context i Rol

Ets un desenvolupador PHP/Laravel expert en el projecte intranetBatoi.
Namespace arrel: `Intranet\`. Models a `app/Entities/`, domain a `app/Domain/`.
Llegeix `AGENTS.md` i `docs/agents/conventions.md` abans de generar res.

## Tasca

Crea l'entitat **{{NomEntitat}}** amb la seua migració, Policy i interfície de repositori.

## Especificacions

- Taula BD: `{{nom_taula}}`
- Camps: `{{camp: tipus (nullable?), …}}`
- Relacions: `{{NomEntitat belongsTo/hasMany NomRelació, …}}`
- Soft deletes: `{{sí / no}}`
- Policy necessària: `{{sí / no}}`
- Domini al que pertany: `{{app/Domain/NomDomini/}}`

## Criteris de qualitat

- Model a `app/Entities/{{NomEntitat}}.php`, namespace `Intranet\Entities`.
- Migració amb `up()` i `down()` complets.
- PHPDoc a la classe i a cada propietat `$fillable`/`$casts`.
- Si hi ha Policy: registrar-la a `AuthServiceProvider` en `$policies`.
- Interfície de repositori a `app/Domain/{{NomDomini}}/{{NomEntitat}}RepositoryInterface.php`.

## Format de resposta

1. `app/Entities/{{NomEntitat}}.php`
2. `database/migrations/{{timestamp}}_create_{{nom_taula}}_table.php`
3. `app/Policies/{{NomEntitat}}Policy.php` *(si s'ha demanat)*
4. Fragment a afegir a `AuthServiceProvider::$policies` *(si s'ha creat Policy)*
5. `app/Domain/{{NomDomini}}/{{NomEntitat}}RepositoryInterface.php`
