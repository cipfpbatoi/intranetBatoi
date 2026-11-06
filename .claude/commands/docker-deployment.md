# /docker-deployment — Desplegament Docker de la intranet

> **Adaptador de Claude Code.** La font canònica és [`docs/operations/docker-deployment.md`](../../docs/operations/docker-deployment.md). Este fitxer només afig el trigger slash i `$ARGUMENTS`.

Aplica només la fase demanada:

```
$ARGUMENTS
```

Les fases són `desenvolupament`, `build` o `servidor`. Llig la guia canònica abans d'actuar. En desenvolupament, pregunta si es volen dades de prova abans d'executar cap seed. No uses `npm ci` ni `npm run dev`; respecta `WWWUSER`/`WWWGROUP`; i demana confirmació immediatament abans de publicar una imatge, fer `pull` o actualitzar un servidor.
