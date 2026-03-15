# Sprint 3 - Migració funcional incremental

## Context
Sprint orientat a estabilitzar fluxos funcionals crítics en Laravel 12 i avançar la migració de JS legacy de forma incremental, amb convivència temporal de codi antic i modern.

Issue mare relacionada: #79

## Objectius del sprint
- Garantir estabilitat funcional en fluxos crítics (FCT, Signatura, APIs associades, auth/token).
- Migrar JS de mòduls prioritaris de jQuery a JavaScript modern, sense regressions.
- Reduir dependències legacy amb evidència de proves.

## Estat real del sprint

Durant l'execució, l'sprint s'ha orientat sobretot a una migració funcional incremental de panells de Direcció a Livewire, mantenint convivència amb el legacy.

Resultat actual:

- estabilització funcional Laravel 12 i auth/token: avançada
- migració JS/jQuery: parcial
- pilots Livewire de Direcció: avançats
- retirada completa del legacy: encara pendent

Pilots creats:

- `direccion/falta-livewire`
- `direccion/comision-livewire`
- `direccion/actividad-livewire`
- `direccion/expediente-livewire`

Documentació de retirada progressiva creada:

- `docs/sprints/sprint-3-p2-comision-legacy-retirement.md`
- `docs/sprints/sprint-3-p3-falta-legacy-retirement.md`
- `docs/sprints/sprint-3-p4-actividad-legacy-retirement.md`
- `docs/sprints/sprint-3-p5-expediente-legacy-retirement.md`

## Estat per línia de treball

### L1 - Estabilització funcional Laravel 12
Estat: Molt avançada

Inclou:

- ajustos d'auth/token
- correccions de fluxos crítics
- regressió manual acumulada del treball funcional

### L2 - Migració JS legacy / jQuery
Estat: Parcial

Inclou:

- reducció de dependència en mòduls prioritaris
- infraestructura comuna JS millorada
- però no hi ha retirada total de jQuery ni de tot el JS legacy

### L3 - Pilots Livewire de Direcció
Estat: Avançada

Inclou:

- pilots funcionals de `falta`, `comision`, `actividad` i `expediente`
- proves específiques per component
- convivència temporal amb el panell legacy

### L4 - Retirada progressiva de legacy
Estat: En preparació activa

Inclou:

- inventari de dependències legacy per mòdul
- plans de desmantellament per fases
- encara sense desacoblament complet de controllers i rutes històriques

## Backlog prioritzat

### S3-01 Audit JS legacy i dependències
Prioritat: Alta
Estat: Fet parcialment i reconduït

Tasques:
- Inventariar usos de jQuery i plugins per pantalla/fitxer.
- Classificar riscos (alt, mitjà, baix) i impacte funcional.
- Identificar mòduls crítics de primera onada de migració.

Criteris d'acceptació:
- Document curt amb mapa `fitxer -> dependències -> risc -> prioritat`.
- Llista tancada de pantalles crítiques a migrar primer.

### S3-02 Token/Auth estable en web + API
Prioritat: Alta
Estat: Molt avançat

Tasques:
- Consolidar flux Bearer Sanctum en web interna.
- Verificar renovació de token en sessió i no ús accidental de tokens caducats.
- Validar endpoints crítics protegits amb `auth:sanctum`.

Criteris d'acceptació:
- Sense 401 espuris en fluxos crítics.
- Proves manuals OK en `/signatura` i endpoints relacionats.

### S3-03 Migració vertical Signatura (jQuery -> JS modern)
Prioritat: Alta
Estat: Avançat

Tasques:
- Migrar `public/js/Signatura/index.js` a JS modern (`fetch`, events nadius).
- Mantindre comportament funcional equivalent en modal i càrrega d'elements.
- Gestionar errors de xarxa/auth de forma explícita.

Criteris d'acceptació:
- Botons A1/A5/A3 funcionals.
- Càrrega de taula i enviament sense regressions.

### S3-04 Migració vertical FCT crítica
Prioritat: Alta
Estat: Parcial / pendent de rematada

Tasques:
- Migrar scripts FCT de major ús (grid/modal/accions principals).
- Reutilitzar capa comuna d'API/auth per evitar duplicació.

Criteris d'acceptació:
- Flux FCT habitual de tutor sense errors funcionals.
- Sense dependència directa de `$` en els fitxers migrats.

### S3-05 Capa comuna JS d'infra
Prioritat: Mitjana-Alta
Estat: Avançada

Tasques:
- Definir `apiClient` compartit (headers, Bearer, errors, parse).
- Definir helpers mínims de DOM/events per a codi comú.

Criteris d'acceptació:
- Nous mòduls migrats utilitzen utilitats comunes.
- Reducció de codi duplicat en peticions API.

### S3-06 Proves i regressió
Prioritat: Alta
Estat: Avançada

Tasques:
- Crear checklist de regressió funcional per mòduls migrats.
- Executar proves manuals i, on siga viable, automatitzades.
- Registrar incidències i traçabilitat contra issues.

Criteris d'acceptació:
- Checklist completada per cada vertical migrada.
- 0 regressions crítiques obertes al tancament del sprint.

### S3-07 Retirada parcial jQuery
Prioritat: Mitjana
Estat: Parcial

Tasques:
- Eliminar imports/usos de jQuery en mòduls ja migrats.
- Mantindre convivència només on encara no s'ha migrat.

Criteris d'acceptació:
- Reducció mesurable d'ús de jQuery en àmbit Sprint 3.
- Cap pantalla crítica del sprint depén de jQuery per funcionar.

### S3-08 Pilots Livewire de Direcció
Prioritat: Alta
Estat: Avançada

Tasques:
- Construir pilot Livewire de `falta.direccion`.
- Construir pilot Livewire de `comision.direccion`.
- Construir pilot Livewire de `actividad.direccion`.
- Construir pilot Livewire de `expediente.direccion`.
- Mantindre convivència amb rutes i controllers legacy.

Criteris d'acceptació:
- Cada panell nou és accessible per una ruta pròpia sense trencar la legacy.
- Hi ha filtre, accions bàsiques i visualització funcional per a Direcció.
- Cada pilot té almenys una prova específica.

### S3-09 Pla de retirada progressiva del legacy
Prioritat: Alta
Estat: Avançada

Tasques:
- Documentar dependències entre pilots nous i controllers legacy.
- Identificar quines peces poden quedar com a bridge temporal.
- Definir ordre de desacoblament per mòdul.

Criteris d'acceptació:
- Existeix un document de retirada per cada pilot principal.
- Queda clar què no es pot eliminar encara i per què.
- Hi ha un següent pas tècnic accionable per a cada mòdul.

## Ordre d'execució recomanat
1. S3-01
2. S3-02
3. S3-03
4. S3-05 (en paral·lel amb S3-03/S3-04)
5. S3-04
6. S3-06
7. S3-07

## Ordre real executat
1. Estabilització funcional Laravel 12 i auth/token
2. Migració/reducció parcial de JS legacy en fluxos crítics
3. Pilot Livewire `falta.direccion`
4. Pilot Livewire `comision.direccion`
5. Pilot Livewire `actividad.direccion`
6. Pilot Livewire `expediente.direccion`
7. Documents de retirada progressiva del legacy

## Punt de tall actual

L'sprint es pot considerar avançat a nivell funcional, però no complet a nivell d'eliminació de legacy.

Per donar-lo per realment tancat, el següent bloc natural és:

- desacoblar accions globals dels controllers legacy
- decidir quins pilots necessiten CRUD complet
- només després plantejar la retirada visible de rutes i panells antics

## Definició de fet (DoD) Sprint 3
- Fluxos crítics estabilitzats en Laravel 12.
- Mòduls prioritaris migrats a JS modern sense regressions.
- Autenticació API coherent amb Sanctum.
- Evidència de proves i traçabilitat en issues/commits.

## DoD realista després d'esta execució
- Hi ha pilots Livewire funcionals per als principals panells de Direcció.
- Hi ha proves específiques per als pilots creats.
- El legacy està identificat i documentat, encara que no completament retirat.
- El següent pas tècnic per a desacoblar legacy és clar en cada mòdul.
