# Plantilla: Nou controlador

> Copia, substitueix `{{...}}` i envia.

---

## Context i Rol

Ets un desenvolupador PHP/Laravel expert en el projecte intranetBatoi.
Namespace arrel: `Intranet\`. Stack: Laravel, Blade, Bootstrap 4, Livewire 3.
Llegeix `AGENTS.md` i `docs/agents/conventions.md` abans de generar res.

## Tasca

Crea un controlador per a **{{nom de la funcionalitat}}** (rol: `{{rol: profesor | alumno | direccion | administrador | …}}`).

## Especificacions

- Tipus: `{{BaseController CRUD | controlador personalitzat}}`
- Model/Entitat: `{{NomEntitat}}` (a `app/Entities/`)
- Ruta base: `{{prefix/nom}}` al fitxer `routes/{{rol}}.php`
- Camps de la graella (`$gridFields`): `{{camp1, camp2, …}}`
- Camps del formulari (`$formFields`): `{{camp1, camp2, …}}` *(només si hi ha modal d'edició)*
- Policy: `{{sí / no — nom: NomPolicy}}`
- Filtrat per professor (`idProfesor`): `{{sí / no}}`

## Criteris de qualitat

- Extén `Core/BaseController` si és CRUD estàndard; `Controller` base en cas contrari.
- Inclou PHPDoc al cap de la classe i als mètodes públics.
- Les cadenes visibles en Blade han d'estar en Valencià.
- Cap lògica de negoci en el controlador: delega a `Application/{{Domini}}/{{Servei}}`.

## Format de resposta

1. Fitxer `app/Http/Controllers/{{Rol}}/{{Nom}}Controller.php` complet.
2. Fragment de ruta a afegir a `routes/{{rol}}.php`.
3. Si s'ha creat una Policy nova, fitxer `app/Policies/{{Nom}}Policy.php`.
4. Llista de passos de registre (ServiceProvider, etc.) si n'hi ha.
