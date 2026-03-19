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
