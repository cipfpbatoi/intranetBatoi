# Sprint 7: Translation Catalog Cleanup

## Objectiu

Sanejar els catàlegs de `resources/lang` després del Sprint 6, deixant una base coherent per a futures migracions sense fer encara una reescriptura massiva.

## Diagnòstic inicial

### Estructura de fitxers

- `ca`: `messages.php`, `models.php`, `pagination.php`, `validation.php`
- `es`: `messages.php`, `models.php`, `pagination.php`, `validation.php`
- `en`: `auth.php`, `messages.php`, `pagination.php`, `passwords.php`, `validation.php`

Conclusió:

- no hi ha paritat estructural entre idiomes
- `en` no tenia `models.php`
- `ca` i `es` no tenien `auth.php` ni `passwords.php`
- hi ha fitxers residuals `.DS_Store` en `resources/lang` i `resources/lang/es`

### Diferències de claus

#### `messages.php`

- `ca`: 352 claus
- `es`: 354 claus
- `en`: 357 claus

Desviacions detectades:

- `es` té claus extra respecte a `ca`:
  - `generic.falta`
  - `menu.afternoon`
- `en` té claus extra respecte a `ca`:
  - `buttons.departamento`
  - `generic.falta`
  - `menu.Authprogram`
  - `menu.Departamento`
  - `menu.Dual`

#### `models.php`

- `ca`: 429 claus
- `es`: 433 claus
- `en`: 0 claus perquè no existix el fitxer

Desviacions detectades:

- `es` té claus extra respecte a `ca`:
  - `Alumnofct.create`
  - `Fct.an5`
  - `Fct.convalidacion`
  - `modelos.Ppoll`
- `en` no té encara catàleg `models.php`

#### `validation.php`

- `ca`: 230 claus
- `es`: 235 claus
- `en`: 243 claus

Desviacions detectades:

- `es` té claus extra respecte a `ca`:
  - `attributes.NFcts`
  - `attributes.evaluacion`
  - `attributes.modulo`
  - `empty_option.departamento`
  - `empty_option.idioma`
- `en` té claus extra respecte a `ca`, principalment claus estàndard de Laravel:
  - `after_or_equal`
  - `before_or_equal`
  - `dimensions`
  - `distinct`
  - `file`
  - `in_array`
  - `ipv4`
  - `ipv6`

#### Altres fitxers

- `pagination.php`: paritat completa
- `auth.php` i `passwords.php`: ja existixen en `ca`, `es` i `en`
- `reminders.php`: residual legacy, sense ús detectat al projecte ni al framework actual

## Lectura pràctica

- El problema principal ja no és d'API d'ús, sinó de govern del catàleg.
- `ca` continua sent la millor base real del projecte, però no és una base completa ni contractual.
- `es` i `en` no sols divergixen en textos, sinó també en estructura i cobertura.
- Abans de pensar en JSON, cal arreglar paritat, naming i fitxers font.

## Tall recomanat

### Tall A

- eliminar `.DS_Store` de `resources/lang`
- documentar `ca` com a base funcional provisional
- decidir quins fitxers han d'existir obligatòriament en tots els idiomes

### Tall B

- restaurar paritat estructural mínima:
  - crear `en/models.php`
  - crear `ca/auth.php`, `ca/passwords.php`, `es/auth.php` i `es/passwords.php`
  - retirar `reminders.php` si es confirma que és legacy sense ús

### Tall C

- alinear claus extra o desviades entre `ca`, `es` i `en`
- separar:
  - claus que falten
  - claus sobrants
  - claus històriques amb naming incoherent

Estat actual de Tall C:

- hi ha naming heterogeni en `messages.php`, sobretot en `buttons.*` i `menu.*`
- conviuen claus en `PascalCase`, `camelCase` i minúscula plana
- hi ha valors duplicats entre dominis diferents (`buttons`, `generic`, `menu`, `rol`)
- encara no s'ha renombrat cap clau, perquè això obliga a tocar consumidors i pot generar regressions
- s'ha afegit una auditoria reusable en `scripts/lang-audit.php` per detectar:
  - extras i missings respecte a `ca`
  - claus sospitoses per naming
  - valors duplicats dins de `messages.php`

### Tall D

- definir criteri estable:
  - PHP per catàlegs de domini (`models`, `validation`, `messages`)
  - JSON només si apareix una necessitat real per a frases lliures d'UI

Estat actual de Tall D:

- ja està inventariat el risc principal de `messages.php`
- `messages.menu.*` té consum dinàmic via `MenuService` i `Menu::getDescripcionAttribute()`
- `messages.buttons.*` té consum dinàmic via `Boton::translateText()`
- `Panel::setBotonera()` i `Pestana::getLabel()` amplien encara més el consum implícit de `buttons.*`
- conseqüència:
  - no es poden podar claus de `menu.*` ni `buttons.*` només amb `rg`
  - abans de normalitzar naming caldrà creuar `menu.*` amb `menus.nombre`
  - i `buttons.*` amb les accions efectives de `Boton`, `Panel`, pestanyes i configs de model

### Tall E

- preparar una neteja de claus mortes i duplicades
- deixar mecanisme de comprovació per no tornar a desalinear idiomes

## Fora d'abast

- retraducció completa de tot el producte
- revisió lingüística exhaustiva de tots els textos
- migració massiva a JSON en este sprint
