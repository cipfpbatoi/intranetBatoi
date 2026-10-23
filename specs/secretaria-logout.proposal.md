# Proposta: permetre el logout del rol Secretaria

- Estat: `ready_to_commit`
- Domini: autenticació i autorització de rols
- Punt d'entrada: `routes/profesor.php` i `Auth\\Profesor\\LoginController@logout`

## Context

La ruta web `/logout` està declarada dins de `routes/profesor.php`, que es carrega amb `role:profesor`. Una persona amb únicament el rol Secretaria està autenticada amb el guard `profesor`, però no té el rol Professorat; per això el middleware li denega la petició abans d'arribar al controlador de logout.

## Escenaris

### Escenari 1: Secretaria pot tancar la seua sessió

**Given** una persona autenticada amb únicament el rol Secretaria  
**When** selecciona l'opció de tancar sessió  
**Then** la sessió del guard de professorat es tanca i els tokens de sessió actuals es revoca segons el comportament existent  
**And** és redirigida al formulari d'inici de sessió o a la destinació pública existent.

### Escenari 2: el logout no requerix el rol Professorat

**Given** una persona autenticada amb Secretaria, Professorat o una combinació de rols de personal  
**When** fa la petició a la ruta de logout existent  
**Then** pot tancar la seua pròpia sessió sense necessitat de superar `role:profesor`  
**And** no obté per això accés a cap altra ruta de Professorat.

### Escenari 3: es preserva el logout dels altres perfils

**Given** un professor autenticat o un alumne autenticat  
**When** utilitza la seua opció actual de tancar sessió  
**Then** es manté el comportament existent i el guard corresponent és el que es tanca.

## Regles de negoci

- Secretaria pot tancar únicament la sessió pròpia; no s'amplia l'accés a rutes de Professorat.
- La ruta de logout del personal ha d'estar protegida per autenticació del guard de professorat, no pel rol Professorat.
- Es conserva el nom/ruta que utilitza actualment la interfície per a no trencar enllaços existents.
- El logout d'alumnat no canvia.

## Fitxers previsibles

- `routes/profesor.php` i `app/Providers/RouteServiceProvider.php` — ubicar/protegir la ruta del personal amb el guard d'autenticació adequat.
- `app/Http/Controllers/Auth/Profesor/LoginController.php` — preservar la revocació de tokens, tancament de guard i redirecció actuals.
- `tests/Feature/` — cobrir logout de Secretaria sola, Professorat i alumnat, i assegurar que Secretaria continua sense accés a rutes de Professorat.

## Riscos

- La ruta `/logout` té un nom utilitzat per la navegació existent; en moure-la cal evitar noms duplicats i conservar l'URL.
- El tancament de sessió del personal també revoca el token de sessió API actual; el canvi no ha d'ometre eixa revocació.

Status: ready_to_commit
