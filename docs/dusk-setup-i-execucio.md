# Laravel Dusk: setup i execució en aquest projecte

## Blocador actual

En aquest entorn no es pot instal·lar paquets de Composer per falta de resolució DNS cap a `repo.packagist.org`.

## Instal·lació (a la teua màquina, amb internet)

1. Instal·lar Dusk:
```bash
composer require --dev laravel/dusk:^8.0
```

2. Instal·lar estructura Dusk:
```bash
php artisan dusk:install
```

3. Generar APP key de l'entorn Dusk (si no existeix):
```bash
cp .env .env.dusk.local
php artisan key:generate --env=dusk.local
```

4. Ajustar `.env.dusk.local`:
- base de dades de proves
- `APP_URL` (p.ex. `http://127.0.0.1:8000`)
- mail/queue en mode de test

## Execució bàsica

1. Arrancar app:
```bash
php artisan serve --port=8000
```

2. Executar Dusk:
```bash
php artisan dusk
```

3. Executar un test concret:
```bash
php artisan dusk --filter=AuthSmokeTest
```

## Primera suite recomanada

1. `AuthSmokeTest`
- login professor correcte
- redirecció a home

2. `ApiBearerSmokeTest`
- obrir pàgina amb API
- verificar resposta ok en acció principal (Comissió o Guardia)

3. `ReservaSmokeTest`
- carregar reserves
- fer una reserva simple

4. `FctSmokeTest`
- carregar llista FCT
- obrir modal i guardar acció senzilla

## Quan acabe la instal·lació

Quan em confirmes que `php artisan dusk:install` ja funciona, et cree immediatament:

1. `tests/Browser/DuskTestCase.php`
2. 3-4 tests Dusk inicials (smoke crítics)
3. selectors estables (`dusk=""`) en les vistes on calga

