---
name: intranet-batoi-fct
description: Guia per treballar en els fluxos FCT de la intranet Batoi. Use when Codex is asked to modify, debug, test, review, or explain FCT features, including alumnado FCT, empresas, centros, instructores, colaboraciones, anexos/annexos, SAO downloads, SignaturaController, /signatura, document PDFs, FCT emails, Annex I/II/III/V, acts, qualifications, or Selenium automation for FCT documents.
---

# Intranet Batoi FCT

## Workflow

1. Read `AGENTS.md` and, if needed, also use `intranet-batoi-general`.
2. Identify whether the request is about FCT data, document generation, SAO downloads, signatures, email text, or evaluation/acts.
3. Start from the route:
   - Professor routes: `routes/profesor.php`.
   - Shared signatura upload/PDF routes: `routes/todos.php`.
   - API documentation/signature routes: `routes/api.php`.
   - Direcció signature routes: `routes/direccion.php`.
4. Trace controller, model relations, services, views, and mail templates before editing.
5. Preserve existing send/signature state semantics unless the user explicitly asks to change workflow behaviour.
6. For text sent to instructors, make obligations explicit: who signs, what document, and whether it must be returned.

## References

- For FCT routes, controllers, entities, documents, and emails, read `references/fct-map.md`.
- For `/signatura`, Annex I/II/III/V, `sendTo`, `signed`, and mail selection, read `references/signatures.md`.
- For SAO/Selenium FCT downloads, read `references/sao-selenium.md`.
