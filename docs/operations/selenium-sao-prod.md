# Servei Selenium per a SAO en producció

## Objectiu

Selenium dona suport als fluxos que entren en SAO/ITACA des de la intranet, sobretot la descàrrega i signatura d'annexos FCT. En producció el servei corre com a contenidor separat i Laravel s'hi connecta per HTTP.

Fitxers clau:

- `docker-compose.prod.yml`: serveis `laravel.prod` i `selenium`, volum compartit `selenium-downloads`.
- `app/Services/Automation/SeleniumService.php`: connexió amb Selenium i login SAO/ITACA.
- `app/Sao/Support/SaoRunner.php`: obri driver, executa l'acció SAO i tanca sessió.
- `app/Sao/Documents/*DocumentService.php`: descàrrega i processat d'annexos.
- `config/sao.php` i `config/services.php`: URLs, esperes i directori de descàrrega.

## Serveis i volums

En producció, `docker-compose.prod.yml` defineix:

```yaml
laravel.prod:
  volumes:
    - selenium-downloads:/srv/documentsfct
  depends_on:
    - selenium

selenium:
  image: selenium/standalone-firefox:latest
  ports:
    - '${FORWARD_SELENIUM_PORT:-4444}:4444'
    - '127.0.0.1:${FORWARD_SELENIUM_VNC_PORT:-7900}:7900'
  volumes:
    - selenium-downloads:/home/seluser/Downloads
```

El navegador descarrega en `/home/seluser/Downloads` dins del contenidor Selenium. Laravel veu el mateix volum en `/srv/documentsfct`.

Variables recomanades en `.env`:

```env
SELENIUM_URL=selenium:4444
SELENIUM_URL_SAO=https://foremp.edu.gva.es/index.php
SELENIUM_URL_ITACA=https://acces.edu.gva.es/sso/login.xhtml?callbackUrl=https://acces.edu.gva.es/escriptori/
SAO_DOWNLOAD_DIR=/srv/documentsfct
SAO_DOWNLOAD_WAIT_SECONDS=10
SAO_NAVIGATION_SLEEP_SECONDS=1
```

No publiques el port VNC fora de localhost. En producció el patró recomanat és `127.0.0.1:7900:7900` i accés per túnel SSH si cal veure el navegador.

## Arrancada

Des del directori de desplegament:

```bash
docker compose -f docker-compose.prod.yml pull
docker compose -f docker-compose.prod.yml up -d selenium laravel.prod
docker compose -f docker-compose.prod.yml ps
```

Comprovacions mínimes:

```bash
docker compose -f docker-compose.prod.yml exec laravel.prod php artisan config:clear
docker compose -f docker-compose.prod.yml exec laravel.prod php artisan tinker
```

En `tinker`, comprova que Laravel resol el host de Selenium:

```php
config('services.selenium.url'); // selenium:4444
config('sao.download.directory'); // /srv/documentsfct
```

## Flux de depuració

1. Revisa estat dels contenidors:

```bash
docker compose -f docker-compose.prod.yml ps
docker compose -f docker-compose.prod.yml logs --tail=100 selenium
docker compose -f docker-compose.prod.yml logs --tail=100 laravel.prod
```

2. Revisa logs SAO de Laravel:

```bash
docker compose -f docker-compose.prod.yml exec laravel.prod tail -n 200 storage/logs/sao.log
docker compose -f docker-compose.prod.yml exec laravel.prod tail -n 200 storage/logs/laravel.log
```

3. Comprova el volum compartit:

```bash
docker compose -f docker-compose.prod.yml exec selenium ls -lah /home/seluser/Downloads
docker compose -f docker-compose.prod.yml exec laravel.prod ls -lah /srv/documentsfct
```

Si el fitxer apareix en Selenium però no en Laravel, el problema és el muntatge del volum. Si no apareix en Selenium, el problema és navegació, credencials, selector o descàrrega bloquejada.

4. Mira el navegador amb VNC només si cal:

```bash
ssh -L 7900:127.0.0.1:7900 usuari@servidor
```

Després obri `http://127.0.0.1:7900` des del teu equip. No deixes este port exposat públicament.

## Errors habituals

- `No s'ha pogut connectar al servidor de Selenium`: `SELENIUM_URL` no apunta a `selenium:4444`, el servei no està alçat o no comparteix xarxa Docker amb Laravel.
- `Password no vàlid`: credencial SAO/ITACA incorrecta o portal extern rebutjant l'accés.
- Descàrrega no trobada: revisa `SAO_DOWNLOAD_DIR`, permisos del volum i `SAO_DOWNLOAD_WAIT_SECONDS`.
- Procés penjat: comprova VNC, logs de `sao.log` i canvis en selectors del portal SAO.
- `storage/logs/sao.log` buit: l'acció pot estar fallant abans d'entrar en el flux SAO; revisa `laravel.log`.

## Reinici controlat

Per reiniciar només Selenium:

```bash
docker compose -f docker-compose.prod.yml restart selenium
```

Per actualitzar la imatge i conservar volums:

```bash
docker compose -f docker-compose.prod.yml pull selenium
docker compose -f docker-compose.prod.yml up -d selenium
```

No esborres el volum `selenium-downloads` durant una incidència si encara necessites inspeccionar fitxers descarregats.

## Checklist postcanvi

- `docker compose -f docker-compose.prod.yml ps` mostra `selenium` i `laravel.prod` en estat saludable.
- Laravel resol `SELENIUM_URL=selenium:4444`.
- `SAO_DOWNLOAD_DIR` apunta a `/srv/documentsfct`.
- El volum `selenium-downloads` està muntat en els dos serveis.
- El port VNC només està exposat en `127.0.0.1`.
- S'han revisat `storage/logs/sao.log` i `storage/logs/laravel.log` després d'una prova real.
