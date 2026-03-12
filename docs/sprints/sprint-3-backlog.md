# Sprint 3 - Migració funcional incremental

## Context
Sprint orientat a estabilitzar fluxos funcionals crítics en Laravel 12 i avançar la migració de JS legacy de forma incremental, amb convivència temporal de codi antic i modern.

Issue mare relacionada: #79

## Objectius del sprint
- Garantir estabilitat funcional en fluxos crítics (FCT, Signatura, APIs associades, auth/token).
- Migrar JS de mòduls prioritaris de jQuery a JavaScript modern, sense regressions.
- Reduir dependències legacy amb evidència de proves.

## Backlog prioritzat

### S3-01 Audit JS legacy i dependències
Prioritat: Alta

Tasques:
- Inventariar usos de jQuery i plugins per pantalla/fitxer.
- Classificar riscos (alt, mitjà, baix) i impacte funcional.
- Identificar mòduls crítics de primera onada de migració.

Criteris d'acceptació:
- Document curt amb mapa `fitxer -> dependències -> risc -> prioritat`.
- Llista tancada de pantalles crítiques a migrar primer.

### S3-02 Token/Auth estable en web + API
Prioritat: Alta

Tasques:
- Consolidar flux Bearer Sanctum en web interna.
- Verificar renovació de token en sessió i no ús accidental de tokens caducats.
- Validar endpoints crítics protegits amb `auth:sanctum`.

Criteris d'acceptació:
- Sense 401 espuris en fluxos crítics.
- Proves manuals OK en `/signatura` i endpoints relacionats.

### S3-03 Migració vertical Signatura (jQuery -> JS modern)
Prioritat: Alta

Tasques:
- Migrar `public/js/Signatura/index.js` a JS modern (`fetch`, events nadius).
- Mantindre comportament funcional equivalent en modal i càrrega d'elements.
- Gestionar errors de xarxa/auth de forma explícita.

Criteris d'acceptació:
- Botons A1/A5/A3 funcionals.
- Càrrega de taula i enviament sense regressions.

### S3-04 Migració vertical FCT crítica
Prioritat: Alta

Tasques:
- Migrar scripts FCT de major ús (grid/modal/accions principals).
- Reutilitzar capa comuna d'API/auth per evitar duplicació.

Criteris d'acceptació:
- Flux FCT habitual de tutor sense errors funcionals.
- Sense dependència directa de `$` en els fitxers migrats.

### S3-05 Capa comuna JS d'infra
Prioritat: Mitjana-Alta

Tasques:
- Definir `apiClient` compartit (headers, Bearer, errors, parse).
- Definir helpers mínims de DOM/events per a codi comú.

Criteris d'acceptació:
- Nous mòduls migrats utilitzen utilitats comunes.
- Reducció de codi duplicat en peticions API.

### S3-06 Proves i regressió
Prioritat: Alta

Tasques:
- Crear checklist de regressió funcional per mòduls migrats.
- Executar proves manuals i, on siga viable, automatitzades.
- Registrar incidències i traçabilitat contra issues.

Criteris d'acceptació:
- Checklist completada per cada vertical migrada.
- 0 regressions crítiques obertes al tancament del sprint.

### S3-07 Retirada parcial jQuery
Prioritat: Mitjana

Tasques:
- Eliminar imports/usos de jQuery en mòduls ja migrats.
- Mantindre convivència només on encara no s'ha migrat.

Criteris d'acceptació:
- Reducció mesurable d'ús de jQuery en àmbit Sprint 3.
- Cap pantalla crítica del sprint depén de jQuery per funcionar.

## Ordre d'execució recomanat
1. S3-01
2. S3-02
3. S3-03
4. S3-05 (en paral·lel amb S3-03/S3-04)
5. S3-04
6. S3-06
7. S3-07

## Definició de fet (DoD) Sprint 3
- Fluxos crítics estabilitzats en Laravel 12.
- Mòduls prioritaris migrats a JS modern sense regressions.
- Autenticació API coherent amb Sanctum.
- Evidència de proves i traçabilitat en issues/commits.
