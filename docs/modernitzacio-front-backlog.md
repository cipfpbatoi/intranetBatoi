# Backlog de Modernització Frontend

Document de treball per planificar la migració de dependències legacy i la modernització de UI.

## 1) Foto actual (resum)

- Bundler: `laravel-mix` + `webpack` (correcte però legacy al projecte).
- Framework UI principal: Bootstrap 4 + Gentelella + jQuery.
- Components SPA: Vue 2 (`resources/assets/js/app.js`).
- Components nous: Livewire (ja hi ha mòduls en producció).
- Legacy global: `resources/assets/js/ppIntranet.js` i `resources/assets/js/custom.js` (molt acoblats a jQuery/DataTables).

## 2) Paquets amb risc / sense manteniment clar

- `vue@2.x` + `vue-template-compiler`: EOL.
- `bootstrap@4.6`: línia antiga.
- `popper.js@1`: legacy (Bootstrap 4).
- `cross-env@5`: molt antiga.
- `resolve-url-loader@3`: antiga.
- `jquery` + scripts legacy (`custom.js`): alt acoblament.
- Variant UI de DataTables `*-bs4`: lligada a Bootstrap 4.

## 3) Estratègia recomanada

- Estratègia híbrida i incremental:
- Mantindre estabilitat de negoci ara.
- Tallar errors globals del bundle legacy per pàgina (com ja s’ha fet en pantalles conflictives).
- Migrar funcionalitat nova a Livewire.
- Migrar components Vue 2 crítics a Vue 3 només on aporte valor clar.
- Deixar Bootstrap 5 per una fase separada i controlada.

## 4) Backlog per fases

### Fase A - Higiene i seguretat de build (baix risc)

- A1: Definir versions de Node/NPM estables en Docker i documentar-les.
- A2: Bloquejar versions conflictives de webpack hash (workaround ja aplicat en `webpack.mix.js`).
- A3: Inventariar pantalles que depenen de `ppIntranet.js` i marcar quines poden anar sense bundle legacy.
- A4: Activar pipeline CI per `npm run dev` i `npm run production` en contenidor.

Impacte:
- Menys incidències de compilació i menys errors JS globals.

### Fase B - Reducció de deute legacy (risc mitjà)

- B1: Partir `ppIntranet.js` per dominis (taules, calendari, formularis, etc.).
- B2: Llevar `console.log` i inicialitzadors globals no necessaris.
- B3: Substituir `moment` en components nous per `dayjs` o API nativa.
- B4: Eliminar càrregues duplicades de `app.js` per vista.

Impacte:
- Menys acoblament global i menys “efectes col·laterals” entre pantalles.

### Fase C - Migració funcional (decisió d’arquitectura)

- C1: Definir criteri: què va a Livewire i què queda en Vue.
- C2: Migrar primer pantalles de control intern (fichar/control, resumen-rango, guardies).
- C3: Crear components reutilitzables de taules/filtres.
- C4: Cobertura amb tests d’integració de fluxos crítics.

Impacte:
- Menys dependència de Vue 2 i més coherència de stack.

### Fase D - Bootstrap 5 (risc alt visual)

- D1: Inventari de classes Bootstrap 4 (`.form-group`, `.input-group-addon`, etc.).
- D2: Migrar DataTables de `*-bs4` a variant compatible amb BS5.
- D3: Revisió visual completa de layouts i formularis.
- D4: Tancament de regressions responsive.

Impacte:
- Gran impacte visual; planificar-ho com a projecte separat.

## 5) Vue 3 vs Livewire (criteri curt)

- Livewire:
- Millor per pantalles CRUD i lògica de formulari orientada a backend Laravel.
- Menys JS manual i menys trencaments de build frontend.

- Vue 3:
- Millor per interaccions riques/temps real/UX més complexa.
- Exigix migració de toolchain i estandardització de components.

Recomanació:
- Prioritzar Livewire en funcionalitat de gestió interna.
- Reservar Vue 3 per mòduls que realment necessiten interacció SPA.

## 6) Impacte esperat per àrea

- Operativa diària: risc baix si es migra per pantalla.
- UX visual: risc mitjà-alt en fase Bootstrap 5.
- Build/deploy: millora clara després de fase A.
- Manteniment: millora progressiva en fases B/C.

## 7) Ordre d’execució suggerit

- Primer: Fase A completa.
- Segon: Fase B en paral·lel amb incidències.
- Tercer: Fase C (migració funcional prioritzada).
- Quart: Fase D (Bootstrap 5) quan la base estiga neta.

## 8) Definició de “Done” per fase

- A: build verd en Docker + docs de versions + zero segfault.
- B: `ppIntranet.js` reduït i sense errors globals crítics en consola.
- C: mínim 3 pantalles migrades i estables (control, resumen-rango, guardies).
- D: UI BS5 validada en fluxos crítics i checklist responsive tancat.
