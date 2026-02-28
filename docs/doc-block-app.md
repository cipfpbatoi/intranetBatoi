# Documentacio d aplicacio amb doc-block

Aquest projecte inclou un generador d index de doc-blocks per tindre una visio rapida de classes i metodes documentats.

## Generar index

```bash
composer docs:app:docblocks
```

El resultat es genera en:

- `docs/app-docblocks-index.md`

## Consulta web

Pots consultar l index des del navegador en:

- `/docs/app-docblocks`

Exemples habituals:

- `http://localhost/docs/app-docblocks`
- `https://localhost/docs/app-docblocks`

## Que inclou

- classes detectades en `app/`
- resum del doc-block de classe
- metodes detectats
- resum del doc-block de cada metode

## Recomanacions

- Mantindre doc-block a totes les classes i metodes publics rellevants.
- Escriure una primera linea de resum clara (sense `@tags`) per millorar l index.
- Regenerar l index en canvis grans de domini o serveis.
