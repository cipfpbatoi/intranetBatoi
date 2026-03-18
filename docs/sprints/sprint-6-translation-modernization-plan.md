# Sprint 6: Translation Modernization

## Objectiu

Iniciar la modernització del sistema de traduccions actual cap a un model més coherent amb Laravel modern, sense fer una migració big bang.

## Abast inicial

- normalitzar l'ús de l'API de traducció en zones modernes
- definir criteri tècnic únic per a noves traduccions
- reduir dependència de patrons legacy en components compartits

## Fora d'abast

- migració completa a JSON
- renombrat massiu de claus històriques
- reescriptura global de tots els catàlegs

## Tall A recomanat

- substituir usos simples de `trans()` per `__()`
- revisar components Blade reutilitzables
- revisar helpers i components nous amb menor risc

## Riscos

- regressions textuals en UI
- dependències ocultes en claus dinàmiques
- inconsistències entre `messages`, `models` i `validation`

## Resultat esperat

Al final del sprint hauria d'existir:

- criteri d'ús estable
- primer bloc de migració fet
- inventari dels punts crítics que no es poden tocar a cegues
