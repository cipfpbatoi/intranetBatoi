# Sprint 1 - Higiene de build i estabilitzacio base

Data: 2026-03-10
Issue: #80

## Objectiu

Estandarditzar l'entorn de compilacio frontend per reduir incidencies de build entre local i contenidor.

## Versions oficials recomanades

- Node.js: `20.x` (`.nvmrc` fixat a `20`)
- npm en contenidor Docker (`docker/8.3/Dockerfile`): `10.8.2`
- npm en local: compatible amb Node 20 (preferible mantindre's en npm 10 per consistencia)
- Bundler: `laravel-mix@^6`
- Webpack: `^5`

## Comandaments oficials de build

### En contenidor (recomanat)

```bash
docker compose exec laravel.test npm ci
docker compose exec laravel.test npm run dev
docker compose exec laravel.test npm run production
```

### En local (alternativa)

```bash
nvm use
npm ci
npm run dev
npm run production
```

## Context del workaround de webpack

En `webpack.mix.js` es manté:

- `output.hashFunction = 'sha256'`

Motiu:
- En alguns entorns Docker/Linux, el hash per defecte de webpack (`xxhash64` en alguns escenaris) ha provocat errors de compilacio.
- `sha256` evita estos crashejos i estabilitza la compilacio.

## Checklist manual de validacio de compilacio i assets

- [ ] `npm ci` finalitza sense errors.
- [ ] `npm run dev` genera `public/js/components/app.js`.
- [ ] `npm run dev` genera `public/css/components/app.css`.
- [ ] `npm run dev` genera `public/js/ppIntranet.js`.
- [ ] `npm run production` finalitza sense `segmentation fault`.
- [ ] `public/mix-manifest.json` conté rutes coherents dels assets.
- [ ] L'aplicacio carrega sense errors JS critics en `login/reset password`.
- [ ] L'aplicacio carrega sense errors JS critics en el layout intranet principal.
- [ ] L'aplicacio carrega sense errors JS critics en una pàgina amb DataTables i una amb formulari.

## Incidencies conegudes en local (mixed content/CORS)

- Si `APP_URL` està en `https://...` pero accedixes per `http://...`, poden aparéixer errors de mixed content en CSS/JS.
- Si backend i frontend es consumeixen amb host/port diferents, pot aparéixer bloqueig CORS en cridades XHR/fetch.
- Recomanacio 1: usar una URL unica coherent (`http://laravel.test` o `https://localhost`) en `.env` i navegador.
- Recomanacio 2: evitar barrejar domini `localhost` i `laravel.test` en la mateixa sessio.

## CI

S'ha afegit workflow:

- `.github/workflows/frontend-build.yml`

Validacions automatiques:

- `npm ci`
- `npm run dev`
- `npm run production`
