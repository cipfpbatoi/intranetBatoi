---
name: intranet-batoi-general
description: Orientació general per treballar en el projecte intranetBatoi. Use when Codex is asked to modify, debug, test, review, document, commit, or explain code in this Laravel intranet, especially tasks involving project structure, routes by role, alerts, Blade views, local conventions, PHPUnit/Dusk, Docker, commits, or Valencià language expectations.
---

# Intranet Batoi General

## Workflow

1. Read `AGENTS.md` first and follow its repository rules.
2. Inspect the relevant route file before changing a controller or view.
3. Prefer existing domain services, finders, policies, presenters, UI helpers, and Blade partials over new abstractions.
4. Keep user-facing text in Valencià unless the existing template is explicitly bilingual.
5. Use `Intranet\Services\UI\AppAlert as Alert` for alerts; avoid reintroducing `Styde\Html\Facades\Alert`.
6. Add or update PHPDoc in modified/new PHP classes and relevant methods/properties.
7. Run the narrowest meaningful checks available. If PHP is unavailable locally, say so clearly.

## References

- For repository layout, commands, coding rules, alerts, language, and commit expectations, read `references/conventions.md`.
- For test and Docker/Selenium notes, read `references/testing-docker.md`.
