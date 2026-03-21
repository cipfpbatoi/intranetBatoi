# Sprint 22 - Extracció de negoci específic de DocumentoController

Issue remot:
- `#124` https://github.com/cipfpbatoi/intranetBatoi/issues/124

## Objectiu

Reduir el caràcter transversal de [`DocumentoController.php`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/DocumentoController.php) i deixar `Documento` com a infraestructura comuna.

## Problema actual

El controlador genèric de documents continua governant massa subfluxos:

- documents generals
- projecte
- qualitat FCT
- parts del flux FCT
- efectes col·laterals sobre altres dominis

## Principi de disseny

- `Documento` ha de quedar com a infraestructura comuna:
  - metadades
  - fitxer/adjunt
  - permisos
  - visibilitat
  - cicle de vida bàsic
- el negoci específic ha d'eixir a serveis o controllers propis

## Tall A. Inventari d'accions candidates

- identificar dins de `DocumentoController`:
  - projecte
  - qualitat
  - operacions vinculades a FCT
  - operacions que toquen notes o estat acadèmic

## Tall B. Tall de frontera

- definir què queda en el controlador genèric
- definir què ha d'eixir a:
  - projecte
  - qualitat FCT
  - documentació FCT

## Tall C. Extracció progressiva

- crear accions o controllers específics per subflux
- mantindre `DocumentoController` només per al contracte documental comú

## Tall D. Impacte en UI

- revisar coherència entre:
  - panell legacy
  - tickets/perfil
  - taula Livewire
- evitar que cada UI torne a encapsular negoci propi

## Resultat esperat

- `DocumentoController` més menut i menys transversal
- millor separació entre infraestructura documental i negoci específic
- millor base per a modularització i simplificació del frontend
