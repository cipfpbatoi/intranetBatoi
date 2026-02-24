# Auditoria de rutes hardcoded (sense nom)

## Resum

Escaneig fet en `app/` i `resources/` per detectar crides directes a URL (`/ruta`) en lloc d'usar `route('name')` o `to_route(...)`.

- `redirect('/...')`: **33**
- `redirect()->action('Controller@method')`: **11**
- `href="/..."` o `href='/...'` en Blade: **55**
- JS amb URL directa (`axios/fetch/location`): **4**

## Zones amb mes risc

### 1) Controllers (redireccions hardcoded)

Fitxers amb mes incidencies:

- `app/Http/Controllers/MenuController.php` (4)
- `app/Http/Controllers/ProfesorController.php` (3)
- `app/Http/Controllers/InstructorController.php` (2)
- `app/Http/Controllers/DocumentoController.php` (2)

Exemples:

- `return redirect("/menu/$elemento->id/edit");`
- `return redirect("/alumno_grupo/" . $new->Grupo()->first()->codigo . "/show");`
- `return redirect("/fct/$fct->idFct/show");`

### 2) Blade (enllacos absoluts)

Fitxers amb mes incidencies:

- `resources/views/horario/propuestas.blade.php` (8)
- `resources/views/empresa/show.blade.php` (6)
- `resources/views/extraescolares/autorizados.blade.php` (3)
- `resources/views/empresa/partials/instructores.blade.php` (3)

Exemples:

- `href="/direccion/horario/propuestas?estado=Pendiente"`
- `href="/empresa/{{$elemento->id}}/edit"`
- `href="/reunion/{!!$formulario->getElemento()->id!!}/borrarProfesor/{!! $profesor->dni !!}"`

### 3) JS

Casos trobats:

- `resources/assets/js/components/fichar/ControlSemanaView.vue`
- `resources/assets/js/components/fichar/BirretItacaView.vue`
- `resources/assets/js/components/guardias/ControlGuardiaView.vue`
- `resources/views/seeder/store.blade.php` (fetch)

Nota: en API interna (`/api/...`) pot ser acceptable, pero convé centralitzar endpoints.

## Recomanacio de treball

Fer-ho **directament, pero en fases curtes** (no tot d'una):

1. Fase A (baix risc): canviar `redirect('/...')` i `redirect()->action(...)` a `to_route(...)`/`redirect()->route(...)`.
2. Fase B (mitja): canviar Blade de fitxers amb mes carrega (`horario/propuestas`, `empresa/show`).
3. Fase C (controlada): revisar URLs en JS i crear constants/helper d'endpoints.

## Criteri de validacio

- Cada canvi ha d'apuntar a una ruta amb `name`.
- Si una ruta no te `name`, primer afegir nom en `routes/*.php`.
- Provar minim:
  - navegacio bàsica dels mòduls tocats
  - tests feature relacionats si existixen

