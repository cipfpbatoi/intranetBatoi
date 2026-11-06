---
name: intranet-batoi-docker-deployment
description: Guia el desplegament Docker de la intranet des d'una màquina de desenvolupament fins a un servidor sense eines de desenvolupament. Usa-la per a preparar, construir, publicar o verificar estes fases.
---

# Desplegament Docker de la Intranet Batoi

Llig primer la recepta canònica [`docs/operations/docker-deployment.md`](../../../docs/operations/docker-deployment.md) i aplica només la fase demanada: `desenvolupament`, `build` o `servidor`.

- En desenvolupament, usa `npm install` i `npm run production`; no substituïsques estes ordres per `npm ci` o `npm run dev`.
- Amb un volum del repositori, obtín `WWWUSER` i `WWWGROUP` amb `id -u` i `id -g` abans d'usar Compose.
- Executa Composer, npm i Artisan amb `docker compose exec --user sail` per respectar la propietat del repositori muntat.
- En la fase de desenvolupament, pregunta una sola vegada si es volen dades i usuari de prova; executa `db:seed` només amb resposta afirmativa i no l'executes mai en `build` o `servidor`.
- No uses `migrate:fresh --seed` fora d'un entorn descartable.
- Abans d'una acció externa (`docker push`, `pull` o actualització del servidor), mostra l'objectiu i demana confirmació explícita.
- No canvies la ruta persistent de `storage` ni esborres volums sense autorització explícita.

Per a Selenium/SAO en producció, llig [`docs/operations/selenium-sao-prod.md`](../../../docs/operations/selenium-sao-prod.md).
