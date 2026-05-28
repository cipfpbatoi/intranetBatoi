---
name: intranet-batoi-fct
description: Guia per treballar en els fluxos FCT de la intranet Batoi. Use when the agent is asked to modify, debug, test, review, or explain FCT features, including alumnado FCT, empresas, centros, instructores, colaboraciones, anexos/annexos, SAO downloads, SignaturaController, /signatura, document PDFs, FCT emails, Annex I/II/III/V, acts, qualifications, or Selenium automation for FCT documents.
---

# Intranet Batoi FCT

## Workflow

1. Llig `AGENTS.md` i, si cal, també la skill `intranet-batoi-general` (o directament `docs/agents/conventions.md`).
2. Identifica si la petició és sobre dades FCT, generació de documents, descàrregues SAO, signatures, text d'email, o avaluació/actes.
3. Comença per la ruta:
   - Rutes de professor: `routes/profesor.php`.
   - Signatura compartida (pujada/PDF): `routes/todos.php`.
   - Documentació/signatura API: `routes/api.php`.
   - Direcció signatura: `routes/direccion.php`.
4. Traça controlador, relacions de model, serveis, vistes i plantilles de correu abans d'editar.
5. Preserva la semàntica existent de `sendTo`/`signed` llevat que l'usuari demane explícitament canviar el workflow.
6. Per al text a instructors, fes explícit qui signa, quin document i si l'han de retornar.

## Referències compartides (`docs/agents/fct/`)

- Rutes FCT, controladors, entitats, documents i correus: [`docs/agents/fct/fct-map.md`](../../../docs/agents/fct/fct-map.md).
- `/signatura`, Annex I/II/III/V, `sendTo`, `signed`, selecció de plantilla: [`docs/agents/fct/signatures.md`](../../../docs/agents/fct/signatures.md).
- Descàrregues SAO i Selenium: [`docs/agents/fct/sao-selenium.md`](../../../docs/agents/fct/sao-selenium.md).
