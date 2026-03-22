# Flux legacy i Laravel12

Este document fixa el flux de treball per minimitzar conflictes entre:

- `laravel11Legacy` (produccio legacy)
- `Laravel12` (evolucio principal + documentacio)

## Regla base

- La documentacio (Swagger i doc-blocks) es manté **només** en `Laravel12`.
- `laravel11Legacy` només rep canvis funcionals urgents (hotfix).

## Procés quan hi ha un hotfix en legacy

1. Aplicar el fix en `laravel11Legacy`.
2. Portar el fix funcional a `Laravel12` (merge o cherry-pick).
3. Validar funcionalitat en `Laravel12`.
4. Regenerar documentacio en `Laravel12`:
   - `composer docs:app:docblocks`
   - (si toca API) regenerar Swagger/OpenAPI.
5. Publicar canvis de `Laravel12`.

## Que NO fer

- No portar canvis de documentacio a `laravel11Legacy`.
- No fer merges de documentacio entre branques legacy i 12.

## Objectiu

Reduir conflictes en fitxers massius de documentacio i mantindre `Laravel12` com a branca font de veritat de docs.
