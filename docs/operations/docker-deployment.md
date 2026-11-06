# Desplegament Docker: desenvolupament i servidor

Esta és la recepta canònica per a agents i persones que preparen la intranet amb Docker. Separa tres rols: màquina de desenvolupament, màquina que construeix/publica la imatge i servidor que només l'executa.

## Invariants

- Usa `npm install`; el lockfile actual no permet garantir `npm ci`.
- Compila els assets amb `npm run production`; `npm run dev` és un servidor Vite, no un artefacte desplegable.
- Quan el repositori es munta com a volum, exporta el UID i GID de l'usuari host abans d'invocar Docker Compose.
- Executa Composer, npm i Artisan amb `docker compose exec --user sail`: sense eixa opció Compose entra com a `root` i Git o els fitxers generats poden tindre propietari incorrecte.
- No inclogues `.env` ni dades de `storage/` en la imatge. El servidor els aporta en temps d'execució.
- No declares completada la inicialització amb seed sense executar-lo: valida estructura, dependències, build, migracions i salut de contenidors, i informa del resultat real del seed.
- Abans de `docker push`, `pull` o `up -d` sobre un servidor, mostra la imatge, l'entorn i l'efecte previst, i demana confirmació explícita.

## 1. Màquina de desenvolupament

Requisits: repositori clonat, Docker Engine i Docker Compose. No assumes que `.env` local existeix ni el substituïsques si ja està configurat.

```bash
cp .env.docker .env
export WWWUSER="$(id -u)"
export WWWGROUP="$(id -g)"
docker compose config --quiet
docker compose up -d --build
docker compose exec -T --user sail laravel.test composer install
docker compose exec -T --user sail laravel.test npm install
docker compose exec -T --user sail laravel.test npm run production
docker compose exec -T --user sail laravel.test php artisan key:generate
docker compose exec -T --user sail laravel.test php artisan migrate
docker compose ps
```

`WWWUSER` i `WWWGROUP` s'han d'exportar en cada shell nova que invoque Compose. Alineen el contenidor `sail` amb l'usuari que posseeix el repositori muntat i eviten fitxers generats amb propietari incorrecte.

### Dades de prova

Abans de migrar una màquina de desenvolupament, pregunta una sola vegada: **«Vols instal·lar les dades i l'usuari de prova?»**. No pressuposes la resposta i no executes cap seed en les fases `build` o `servidor`.

- Si respon que sí, després de `php artisan migrate` executa:

  ```bash
  docker compose exec -T --user sail laravel.test php artisan db:seed
  ```

  Confirma que acaba correctament i indica les credencials de prova que documenta `docs/operations/setup.md`.
- Si respon que no, continua sense `db:seed` i deixa constància que la base queda sense dades de demostració.

No uses `migrate:fresh --seed` en una base existent: elimina totes les dades. Només és una verificació controlada en un entorn de prova descartable.

## 2. Construir i publicar des d'una màquina amb eines de desenvolupament

La imatge de producció es construeix amb [`docker/8.3/Dockerfile.prod`](../../docker/8.3/Dockerfile.prod). Inclou dependències PHP sense desenvolupament, `npm install` i `npm run production`.

```bash
export IMAGE_TAG_PROD="2026.11.06-<versio>"
export WWWUSER="$(id -u)"
export WWWGROUP="$(id -g)"
docker build \
  --build-arg WWWUSER="$WWWUSER" \
  --build-arg WWWGROUP="$WWWGROUP" \
  -f docker/8.3/Dockerfile.prod \
  -t "arturocandela/intranet:${IMAGE_TAG_PROD}" .
docker run --rm --entrypoint /bin/sh "arturocandela/intranet:${IMAGE_TAG_PROD}" -lc 'test -f public/build/manifest.json'
```

Usa etiquetes immutables i descriu exactament la destinació abans de publicar:

```bash
docker push "arturocandela/intranet:${IMAGE_TAG_PROD}"
```

No uses `latest` com a única referència per a una actualització manual o un rollback.

## 3. Servidor sense eines de desenvolupament

El servidor només necessita Docker Engine, Docker Compose, accés al registre d'imatges i un directori de desplegament amb estos fitxers:

```text
<directori-desplegament>/
  docker-compose.prod.yml
  Caddyfile
  .env
  storage/
```

Mantín `.env` fora de la imatge i protegeix-lo. `storage/` ha de persistir entre versions.

```bash
export IMAGE_TAG_PROD="2026.11.06-<versio>"
export WWWUSER="$(id -u)"
export WWWGROUP="$(id -g)"
docker compose -f docker-compose.prod.yml config --quiet
docker compose -f docker-compose.prod.yml pull
docker compose -f docker-compose.prod.yml up -d
docker compose -f docker-compose.prod.yml ps
docker compose -f docker-compose.prod.yml logs --tail=100 laravel.prod caddy
```

No cal clonar el repositori, instal·lar Composer ni instal·lar Node al servidor.

### Ruta de `storage` actual

El `docker-compose.prod.yml` actual munta `storage` des de `/var/www/html/intranetBatoi/storage`, una ruta absoluta específica. Abans del primer desplegament, l'agent ha de comprovar que esta ruta existeix i és persistent. Si el servidor usa un altre directori, ha de parar i demanar autorització per a adaptar el compose; no ha d'inventar una ruta ni crear un muntatge diferent silenciosament.

## Verificació i incidències

Després d'arrancar, comprova que Caddy servix el domini esperat i que els assets de `public/build/` es carreguen. L'aplicació actual no publica `/up`: esta URL respon 404 i el `healthcheck` de `laravel.prod` ha de revisar-se abans d'exigir que el servei aparega saludable. Per a problemes amb Selenium/SAO, consulta [`selenium-sao-prod.md`](selenium-sao-prod.md).

Si fallen permisos, compara `id -u`/`id -g` de l'host amb `WWWUSER`/`WWWGROUP`, revisa propietat de `storage` i `bootstrap/cache`, i mira els logs abans de canviar permisos recursivament. No borres volums ni dades per a "provar" una correcció.

## Adaptadors d'agents

- Codex: skill `intranet-batoi-docker-deployment`.
- Claude Code: `/docker-deployment <desenvolupament|build|servidor>`.
- Altres agents: usa esta guia directament o la plantilla [`prompts/docker-deployment.md`](../../prompts/docker-deployment.md).
