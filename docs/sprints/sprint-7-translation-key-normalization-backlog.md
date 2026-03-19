# Sprint 7: Translation Key Normalization Backlog

## Objectiu

Separar què es pot normalitzar de forma segura del que encara depén de noms dinàmics, menús legacy o accions UI amb risc de regressió.

## Criteri

- no renombrar claus només perquè estiguen duplicades
- prioritzar primer les claus mortes o clarament literals
- evitar canvis sobre `menu.*` i `buttons.*` si poden vindre de noms dinàmics (`Boton`, menús en BBDD, config de models, etc.)

## Duplicats detectats però no podables a cegues

- `Actes`
  - `buttons.Acta`
  - `generic.actas`
  - `menu.Acta`
  - `menu.Actas`
- `Enquestes`
  - `menu.Enquestes`
  - `menu.Poll`
- `Empreses`
  - `menu.Empresa`
  - `menu.Empresas`
- `Equip directiu`
  - `menu.Direccion`
  - `menu.Equipodirectivo`
- `Seguiments`
  - `menu.Controlsegui`
  - `menu.Resultados`
- `Autorització d'horaris`
  - `menu.Authhorarios`
  - `menu.Authpropuesta`
- `Reunions`
  - `generic.reuniones`
  - `menu.Controlreunion`
- `Direcció`
  - `generic.direccion`
  - `rol.direccion`
- `Calendari Escolar`
  - `generic.calendari`
  - `menu.Calendari`
- `Activitats`
  - `generic.actividades`
  - `menu.Acttut`
- `Horari`
  - `buttons.horario`
  - `generic.timeTable`
- `Gestor Documental`
  - `buttons.gestor`
  - `menu.Documento`
- `Accedir com a eixa persona`
  - `buttons.change`
  - `generic.change`
- `Avisar`
  - `buttons.avisar`
  - `buttons.mensaje`
- `Alumnat`
  - `buttons.Alumno`
  - `menu.Alumno`

## Casos revisats

- `buttons.mensaje`
  - no es pot eliminar encara
  - continua viu via accions dinàmiques `profesor.mensaje`, `alumno.mensaje`, etc.
- `menu.Empresas`, `menu.Resultados`, `menu.Equipodirectivo`, `menu.Poll`
  - no tenen ús literal clar
  - poden continuar entrant via menús dinàmics o dades persistides

## Següent tall segur

### Tall D1

- definir nomenclatura objectiu per a claus noves
- no renombrar encara claus històriques vives

### Tall D2

- inventariar noms de menú persistits en BBDD que apunten a `messages.menu.*`
- creuar-los amb les claus actuals abans de tocar `menu.*`

### Tall D3

- inventariar accions de botons que es resolen dinàmicament contra `messages.buttons.*`
- identificar quines claus es poden convertir en alias documentats i quines es poden retirar

## Tall D2 executat

### Consumidors dinàmics confirmats

- `messages.menu.*`
  - no es resol només per ús literal en Blade o PHP
  - [`MenuService.php`](/Users/igomis/Code/intranetBatoi/app/Application/Menu/MenuService.php) traduïx títols amb `messages.menu.<rawKey>`
  - [`Menu.php`](/Users/igomis/Code/intranetBatoi/app/Entities/Menu.php) traduïx descripcions amb `messages.menu.` + `ucwords($this->nombre)`
  - això implica que qualsevol valor persistit en `menus.nombre` pot activar una clau encara que `rg` no la trobe literal
- `messages.buttons.*`
  - es resolen dinàmicament des de [`Boton.php`](/Users/igomis/Code/intranetBatoi/app/UI/Botones/Boton.php)
  - l'`accion` del botó cau per defecte a `messages.buttons.<accio>`
  - també hi ha accions secundàries que venen de `config/modelos.php`

### Claus confirmades com a vives

- `buttons.mensaje`
  - continua viva
  - hi ha rutes actives `profesor.mensaje`, `direccion.mensaje` i `alumno.mensaje`
  - també hi ha botons dinàmics en [`ProfesorController.php`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/ProfesorController.php) i [`AlumnoController.php`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/AlumnoController.php)
- `buttons.avisar`
  - continua viva
  - apareix literalment en modals compartits com [`aviso.blade.php`](/Users/igomis/Code/intranetBatoi/resources/views/intranet/partials/modal/aviso.blade.php) i [`entreFechas.blade.php`](/Users/igomis/Code/intranetBatoi/resources/views/intranet/partials/modal/entreFechas.blade.php)
- `menu.Empresa`
  - continua viva
  - hi ha ús literal en [`empresa/show.blade.php`](/Users/igomis/Code/intranetBatoi/resources/views/empresa/show.blade.php)

### Claus sospitoses però no podables encara

- `menu.Empresas`
- `menu.Equipodirectivo`
- `menu.Poll`
- `menu.Resultados`

Lectura:

- `rg` no troba ús literal clar d'estes claus
- això no demostra que estiguen mortes, perquè poden vindre de dades persistides en la taula `menus`
- fins que no es faça inventari de `menus.nombre`, s'han de considerar aliases vius o com a mínim claus bloquejades

### Criteri que queda fixat

- no renombrar ni esborrar `menu.*` només per falta d'ús literal
- no renombrar ni esborrar `buttons.*` si la clau pot arribar des de `Boton::$accion` o des de config de models
- qualsevol poda futura de `menu.*` requerix creuament previ amb dades persistides

## Tall D3 executat

### Consumidors dinàmics confirmats

- `messages.buttons.*`
  - [`Boton.php`](/Users/igomis/Code/intranetBatoi/app/UI/Botones/Boton.php) resol el fallback de text a `messages.buttons.<accio>`
  - [`Panel.php`](/Users/igomis/Code/intranetBatoi/app/UI/Panels/Panel.php) genera botoneres per defecte a partir de `model.accio`
  - [`Pestana.php`](/Users/igomis/Code/intranetBatoi/app/UI/Panels/Pestana.php) també prova `messages.buttons.<nom>`
- conseqüència:
  - una clau `buttons.*` pot estar viva encara que no aparega literalment en Blade
  - no es pot deduir mort només amb `rg`

### Famílies d'accions confirmades com a vives

- CRUD i navegació base
  - `create`, `show`, `edit`, `delete`, `init`, `link`, `pdf`, `active`
- accions de notificació o workflow
  - `mensaje`, `avisar`, `notification`, `read`, `resolve`, `refuse`
- accions específiques de mòdul
  - `do`, `detalle`, `document`, `email`, `ics`, `carnet`, `barcode`, `coordinador`

### Lectura pràctica

- hi ha centenars d'ús potencials de `messages.buttons.*` repartits entre controllers, pestanyes i modals
- a més dels botons manuals, hi ha molta generació implícita des de `Panel::setBotonera()`
- això convertix `buttons.*` en un espai d'alias funcional, no en un catàleg purament literal

### Claus especialment sensibles

- `buttons.show`
- `buttons.edit`
- `buttons.delete`
- `buttons.init`
- `buttons.link`
- `buttons.pdf`
- `buttons.active`
- `buttons.mensaje`
- `buttons.avisar`

Raó:

- no són només textos visibles
- també són convencions de nom d'acció dins del framework intern de panells

### Següent tall segur

- inventariar, si cal, les accions reals usades per `Boton`/`Panel` per separar:
  - claus base de framework intern
  - claus literals d'UI
  - alias històrics que encara depenen de rutes o models
- fins aleshores, qualsevol normalització de `buttons.*` s'ha de limitar a documentar i no a renombrar

## Tall D4 executat

### Creuament real amb `menus.nombre`

S'ha comparat la taula `menus` real del contenidor amb `resources/lang/ca/messages.php`, aplicant la mateixa normalització que usa el codi (`ucwords`).

### Claus persistides en BBDD però absents en `ca`

- `menu.Authbirret`
- `menu.Birret`
- `menu.Controlrango`
- `menu.Departamento`

Lectura:

- `Authbirret` i `Birret` són residu clar de legacy ja retirat del codi, però encara present en dades
- `Departamento` no és legacy obvi; és una absència real de catàleg si encara hi ha menú viu
- `Controlrango` també està present en dades i requerix revisió funcional abans de decidir si es traduïx o si es neteja de BBDD

### Claus de `ca` sense correspondència actual en `menus.nombre`

- `menu.Acompanyant`
- `menu.Actas`
- `menu.Authpropuesta`
- `menu.Borrarprg`
- `menu.Comissio`
- `menu.Consell`
- `menu.Emergencias`
- `menu.Igualtat`
- `menu.List`
- `menu.Moodle`
- `menu.Pga`
- `menu.Pla`
- `menu.Procediment`
- `menu.Programacion`
- `menu.Rri`
- `menu.Usuario`

Lectura:

- no apareixen en la taula `menus` actual
- això no prova que siguen mortes en tots els entorns
- sí que les convertix en candidates de revisió, sobretot si tampoc tenen ús literal ni entrada de seeder

### Criteri que queda fixat

- `menu.Authbirret` i `menu.Birret` apunten a residu de dades, no a necessitat de recuperar legacy
- abans de podar claus `unused_in_db`, convé revisar seeders/config o altres entorns
- `menu.Departamento` i `menu.Controlrango` passen a incidència de catàleg o de dades, no a poda automàtica

## Tall D5 executat

### Ajustos aplicats

- s'ha afegit suport de catàleg per a:
  - `menu.Departamento`
  - `menu.Controlrango`
- s'ha decidit tractar `birret/Authbirret` només com a residu de menús
- s'ha afegit una migració idempotent per eliminar de `menus`:
  - `birret`
  - `Authbirret`

### Estat resultant

- `Departamento` i `Controlrango` deixen de ser absències de `messages.menu.*`
- `Birret/Authbirret` deixen de considerar-se “claus a recuperar”
- la neteja de `birret` queda limitada a menús, no a documentació ni a altres textos residuals

## Tall D6 executat

### Poda segura de `menu.*`

S'ha retirat un primer paquet de claus de menú que complien totes les condicions següents:

- no apareixen en la taula `menus` actual
- no apareixen en seeders, migracions, config funcional, rutes ni codi d'aplicació
- només sobrevivien dins de `resources/lang/*/messages.php`

### Claus eliminades

- `menu.Authpropuesta`
- `menu.Borrarprg`
- `menu.Moodle`
- `menu.Pga`
- `menu.Rri`
- `menu.Emergencias`

### Criteri que queda fixat

- esta poda és segura només perquè hi havia absència simultània en:
  - dades reals
  - codi/config
  - i punts d'entrada de menú
- la resta de claus `unused_in_db` continuen bloquejades fins fer el mateix nivell de comprovació

## Tall D7 executat

### Poda segura addicional de `menu.*`

S'ha retirat un segon paquet de claus que, a més de no existir en `menus`, tampoc tenen coincidències exactes fora del catàleg.

### Claus eliminades

- `menu.List`
- `menu.Igualtat`
- `menu.Procediment`
- `menu.Usuario`

### Claus que continuen bloquejades

- `menu.Acompanyant`
- `menu.Actas`
- `menu.Comissio`
- `menu.Consell`
- `menu.Pla`
- `menu.Programacion`

Lectura:

- encara que no apareguen en la BBDD actual, continuen sent polisèmiques o tenen col·lisions clares amb altres àrees del producte
- no són bona candidata a poda automàtica sense un tall específic de renombrat o consolidació semàntica
