# Desplegament Docker de preproducció i producció

Este document completa la tasca de documentació de la `issue #81` i descriu el flux recomanat per a construir, publicar i desplegar la imatge Docker de producció de la intranet. Els exemples estan inspirats en l'entorn de preproducció actual amb `Caddy`, però s'han simplificat perquè servisquen com a guia general.

## Objectiu

La imatge de producció ha d'incloure el codi de l'aplicació i els assets compilats dins de la pròpia imatge. Això evita muntar el codi com a volum i fa el desplegament més previsible.

Els fitxers base del repositori són:

- `docker/8.3/Dockerfile.prod`: build multi-stage amb Composer i `npm run production`
- `docker-compose.prod.yml`: arranque del contenidor de Laravel sense muntar el codi del projecte
- `.dockerignore`: exclusió de secrets i fitxers que no han d'entrar en el context de build

## Què canvia respecte a desenvolupament

En desenvolupament, [`docker-compose.yml`](/Users/arturo/Documents/desarrollo/intranetBatoi/docker-compose.yml) munta el repositori complet com a volum (`.:/var/www/html`). En producció o preproducció, [`docker-compose.prod.yml`](/Users/arturo/Documents/desarrollo/intranetBatoi/docker-compose.prod.yml) usa una imatge publicada i només munta els elements que han de persistir o injectar-se en runtime:

- el fitxer `.env`
- la carpeta `storage`
- els volums auxiliars de `Caddy`
- el volum compartit de Selenium, si este servei es manté en l'entorn

## Build i push de la imatge

Des de l'arrel del projecte:

```bash
export IMAGE_TAG_PROD=laravel12-prod
docker build -f docker/8.3/Dockerfile.prod -t arturocandela/intranet:${IMAGE_TAG_PROD} .
docker push arturocandela/intranet:${IMAGE_TAG_PROD}
```

Si es vol un desplegament versionat, és preferible usar un tag explícit:

```bash
export IMAGE_TAG_PROD=2026.03.16
docker build -f docker/8.3/Dockerfile.prod -t arturocandela/intranet:${IMAGE_TAG_PROD} .
docker push arturocandela/intranet:${IMAGE_TAG_PROD}
```

La imatge resultant ja incorpora:

- dependències PHP de producció (`composer install --no-dev`)
- assets compilats (`npm run production`)
- codi de l'aplicació copiat dins de `/var/www/html`

## Preparar la màquina de preproducció o producció

Requisits mínims:

- Docker Engine i Docker Compose
- un fitxer `.env` fora del repositori o gestionat de forma segura en el servidor
- una carpeta persistent per a `storage`
- un `Caddyfile` adaptat al domini real de l'entorn

Exemple d'estructura en el servidor:

```text
/opt/intranet/
  docker-compose.prod.yml
  Caddyfile
  .env
  storage/
```

La carpeta `storage/` ha de persistir entre desplegaments perquè conté logs, fitxers pujats i altres artefactes de runtime.

## Variables i fitxers sensibles

No s'ha de copiar mai el `.env` dins de la imatge. En este projecte, això es reforça de dues maneres:

- [`.dockerignore`](/Users/arturo/Documents/desarrollo/intranetBatoi/.dockerignore) exclou `.env*` i només permet `.env.example`
- [`docker-compose.prod.yml`](/Users/arturo/Documents/desarrollo/intranetBatoi/docker-compose.prod.yml) munta `./.env:/var/www/html/.env`

Abans de publicar una imatge convé revisar:

```bash
docker run --rm --entrypoint /bin/sh arturocandela/intranet:${IMAGE_TAG_PROD} -lc 'ls -la /var/www/html | grep .env'
docker history --no-trunc arturocandela/intranet:${IMAGE_TAG_PROD}
```

El primer comandament hauria de mostrar com a màxim `.env` generat des de `.env.example` dins del procés de build, mai un `.env` real del servidor. El segon permet inspeccionar que no s'hagen introduït secrets en cap capa de la imatge.

## Exemple orientatiu de `docker-compose` per a preproducció

L'arxiu del repositori ja inclou una base vàlida. Per a preproducció, l'important és mantindre este patró:

```yaml
services:
  laravel.prod:
    image: arturocandela/intranet:${IMAGE_TAG_PROD:-laravel12-prod}
    restart: unless-stopped
    expose:
      - "80"
    volumes:
      - ./.env:/var/www/html/.env
      - ./storage:/var/www/html/storage

  caddy:
    image: caddy:2-alpine
    restart: unless-stopped
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./Caddyfile:/etc/caddy/Caddyfile:ro
      - caddy_data:/data
      - caddy_config:/config
```

Notes:

- no es munta `.:/var/www/html`
- el codi viatja dins de la imatge
- `storage` queda fora per a conservar dades entre versions
- `Caddy` publica HTTP/HTTPS i fa de proxy invers cap al contenidor Laravel

## Exemple orientatiu de `Caddyfile`

Per a una preproducció interna, el patró és tan senzill com:

```caddy
preintranet.exemple.lan {
  reverse_proxy laravel.prod:80
  tls internal
}
```

Punts clau:

- el domini ha d'apuntar al servidor de preproducció
- el nom del servei (`laravel.prod` o equivalent) ha de coincidir amb el definit en `docker-compose.prod.yml`
- `tls internal` és útil per a entorns interns o de proves
- en producció pública es pot substituir per TLS gestionat per `Caddy` amb un domini resoluble públicament

## Arrancada i actualització

Una vegada copiats `docker-compose.prod.yml`, `Caddyfile`, `.env` i la carpeta `storage` al servidor:

```bash
export IMAGE_TAG_PROD=laravel12-prod
docker compose -f docker-compose.prod.yml pull
docker compose -f docker-compose.prod.yml up -d
```

Per a actualitzar a una nova versió:

```bash
export IMAGE_TAG_PROD=2026.03.16
docker compose -f docker-compose.prod.yml pull
docker compose -f docker-compose.prod.yml up -d
```

## Verificacions posteriors al desplegament

Després d'alçar l'entorn, convé revisar:

- que `Caddy` resol el domini correcte i retorna la intranet
- que l'aplicació arranca sense muntar el codi del repositori
- que `storage` manté els fitxers i logs entre reinicis
- que CSS i JS es carreguen correctament, perquè venen compilats dins de la imatge
- que el `.env` del servidor és l'única font de configuració sensible

Comandes útils:

```bash
docker compose -f docker-compose.prod.yml ps
docker compose -f docker-compose.prod.yml logs -f laravel.prod
docker compose -f docker-compose.prod.yml logs -f caddy
```

## Observacions

- Si es manté Selenium en preproducció per a proves end-to-end, es pot conservar el volum compartit de descàrregues tal com està en l'exemple actual.
- Les comandes de `artisan config:cache` i `route:cache` s'han d'executar en runtime només si l'entorn ja disposa del `.env` definitiu. Si es vol activar eixa optimització, cal fer-ho en l'`entrypoint` després de muntar la configuració real del servidor.
