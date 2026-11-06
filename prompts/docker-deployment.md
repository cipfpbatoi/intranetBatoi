# Desplegament Docker de la intranet

Usa la guia canònica [`docs/operations/docker-deployment.md`](../docs/operations/docker-deployment.md) per a la fase `{{fase: desenvolupament|build|servidor}}`.

Context disponible:

- Màquina i directori: `{{context}}`
- Imatge o etiqueta, si aplica: `{{imatge}}`
- Entorn de destinació: `{{entorn}}`
- Dades de prova: `{{dades_prova: sí|no|pregunta a la persona}}`

Abans d'actuar, comprova els prerequisits i indica les ordres que executaràs. Usa `npm install` i `npm run production`; no uses `npm ci` ni `npm run dev`. Si hi ha un volum del repositori, obtín `WWWUSER` i `WWWGROUP` amb `id -u` i `id -g`, i executa Composer, npm i Artisan com a `sail` (`docker compose exec --user sail`). Considera el seed fallit com una incidència separada.

En desenvolupament, si `dades_prova` no està definit, pregunta una sola vegada si es volen dades i usuari de prova. Amb resposta afirmativa, executa `php artisan db:seed` després de migrar i comunica les credencials documentades; amb resposta negativa, no faces seed. No uses `migrate:fresh --seed` llevat d'una base descartable. Atura't i demana confirmació abans de `docker push`, `docker compose pull` o `docker compose up -d` en un servidor. No esborres volums ni alteres la ruta de `storage` sense autorització explícita.
