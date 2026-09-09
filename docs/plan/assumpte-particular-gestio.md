# Pla de desenvolupament – Gestió d’Assumptes Particulars

## Context
- **Issue:** #315 – *[EPIC] Gestió de permisos per assumptes particulars*
- **Branca:** `feature/assumpte-particular-gestio`

## Objectiu
Implementar el bounded context **AssumpteParticular** que gestioni sol·licituds, aprovacions i denegacions, generi una `Falta` quan s’aprovi, i gestioni notificacions i auditoria, seguint la Instrucció 1/2026 i les polítiques del centre.

## Components clau
| Component | Responsabilitat | Enfocament |
|-----------|-----------------|------------|
| **Entities** (`AssumpteParticular`, `AssumpteDetail`, etc.) | Dades i rius de permissos | Eloquent + Relations |
| **Service** (`AssumpteParticularService`) | Validació de períodes, dies lectius/no lectius, prorrateig, excepcions | Lògica segregada |
| **Controllers** (API + Web) | CRUD, aprovació/denegació, exportació PDF | Endpoints REST & Livewire |
| **Livewire** (`AssumpteParticularForm`, `AssumpteParticularPanel`) | UI interactiva per professorat i direcció | Components 3.x |
| **Mail** (`AssumpteParticularMail`) | Templates de correu per aprovació/denegació | PHP mailer + templates |

## Etapes prioritàries
1. **Modelar domini** – `#316`: definició d’entitat i relacions.
2. **UI** – `#318` (sol·licitud) + `#317` (panell de direcció).
3. **Lògica d’aprovació** – `#319`: servircreació d’`Falta` i resolució.
4. **Notificació i auditoria** – `#320`: correus, logs i PDFs.

## Decisiones pendents
- Localització i format del PDF de la instrucció oficial
- Mètode de firma (certificat digital vs. manual)
- Arrodoniment i repartiment proporcional per torn
- Font de festes locals i períodes d’avaluació
- Format del pla d’activitats (adjunt, text o ambdues opcions)

## Cronograma provisional
| Etapa | Estimació d’hores | Responsable |
|-------|-------------------|-------------|
| Domini | 12 | Dev 1 |
| UI | 8 | Dev 2 |
| Lògica & Tests | 10 | Dev 1 |
| Integració i QA | 6 | QA |

## Referències
- Issue #315 – Gestió d’Assumptes Particulars
- Repositori GitHub – `cipfpbatoi/intranetBatoi`