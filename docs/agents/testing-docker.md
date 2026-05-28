# Proves I Docker

## Comandes

- PHP dependencies: `composer install`.
- JS dependencies: `npm install`.
- Build assets: `npm run dev`, `npm run watch`, `npm run production`.
- App local: `php artisan serve`.
- DB: `php artisan migrate --seed`.
- Tests: `phpunit` o `php artisan test`; usar `--filter` per acotar.

## Scripts Composer

- `composer test:focus`
- `composer test:quick`
- `composer test:full`
- `composer test:auth-migration`
- `composer dusk:local`

## Docker/Selenium

- Llistar contenidors: `docker ps --format "table {{.Names}}\t{{.Image}}\t{{.Status}}"`.
- Contenidor habitual de Selenium: `intranetbatoi-selenium-1`.
- Shell Selenium: `docker exec -it intranetbatoi-selenium-1 /bin/bash`.
- En imatges Selenium oficials, descàrregues del navegador: `/home/seluser/Downloads`.
- Si Docker falla per socket o permisos, demanar escalat de la comanda.
