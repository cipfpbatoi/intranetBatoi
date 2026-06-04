---
name: intranet-batoi-notificacions
description: Guia per treballar en notificacions de panell, avisos, MyMail i enviament de missatges de la intranet Batoi. Use when the agent is asked to modify, debug, test, review, explain, or investigate reuse of NotificationService, AdviseService, AdviseTeacher, MyMail, MailSender, mensajePanel, /notification, /myMail, or the shared messaging system in another Laravel app.
---

# Intranet Batoi Notificacions

## Workflow

1. Llig `AGENTS.md` i la skill `intranet-batoi-general`.
2. Classifica el canal: notificació de panell, avís de domini, correu editable o correu post-signatura/document.
3. Traça entrada, servei, notificació/correu, configuració i tests abans d'editar.
4. Per reutilitzar en una altra app Laravel, inventaria dependències locals i proposa un adaptador o contracte prim abans de copiar classes.
5. Executa els tests acotats del servei afectat (`NotificationServiceTest`, `AdviseServiceTest`, `MailSenderTest`, etc.).

## Referències Compartides

- Mapa de notificacions, avisos i correu: [`docs/agents/notificacions/notificacions-map.md`](../../../docs/agents/notificacions/notificacions-map.md).
- Convencions generals i tests: [`docs/agents/conventions.md`](../../../docs/agents/conventions.md), [`docs/agents/testing-docker.md`](../../../docs/agents/testing-docker.md).
