# Proposta: rol independent de Secretaria

- Estat: `ready_for_review`
- Domini: perfils, autenticació i operativa de convalidacions
- Punt d'entrada: `config/roles.php`, autenticació del perfil de professorat i autorització del llistat per mòdul
- Branca revisada: `323-add-mvp-de-sollicitud-i-gestió-manual-de-convalidacions`

## Context revisat

La branca ja permet consultar les convalidacions per mòdul, generar-ne el PDF i confirmar des del PDF el registre de les peticions acceptades com a `Acceptada (Convalidada)` en la intranet. Ara aquesta autorització depén del DNI de `config('avisos.secretario')`, tant en `ConvalidacioPolicy` com en `MenuService`. Eixe DNI identifica el càrrec de secretari de l'equip directiu, no necessàriament una persona amb el nou rol operatiu de Secretaria.

Els comptes del personal s'autentiquen com a `Profesor` amb el camp `codigo`. El login satisfactori i el primer login redirigixen actualment a `/home`, una ruta protegida per `role:profesor`. El valor `53` no apareix en els rols configurats. La composició de rols és multiplicativa i `todos = 1` és implícit.

La spec general de convalidacions ja limita Secretaria al llistat per mòdul, al PDF i a la confirmació de registre; no li dona accés al detall/resolució ni a la configuració de Direcció. Esta proposta substituïx l'autorització personal per DNI per una autorització basada en el rol, sense eliminar `avisos.secretario` quan s'utilitze per a signatures, documents o altres responsabilitats.

## Escenaris

### Escenari 1: representar Secretaria com a rol independent i combinable

**Given** la codificació multiplicativa de rols existent i que el primer 53 està lliure  
**When** s'assigna Secretaria a un compte  
**Then** el rol únic es representa amb `53`  
**And** Professorat + Secretaria es representa amb `159` (`3 × 53`)  
**And** Direcció + Secretaria es representa amb `106` (`2 × 53`)  
**And** `todos = 1` continua sent implícit i no altera aquests valors  
**And** els helpers de rols reconeixen tots els factors sense concedir-ne cap altre.

### Escenari 2: autenticar-se i arribar a una destinació autoritzada

**Given** un compte de personal amb únicament el rol Secretaria i credencials vàlides  
**When** inicia sessió normalment o completa el primer inici de sessió  
**Then** s'autentica amb el mecanisme existent de codi d'usuari  
**And** arriba al llistat operatiu de convalidacions per mòdul, sense passar per una ruta protegida per `role:profesor`  
**And** pot accedir a les rutes generals protegides per `todos`  
**And** no entra en un bucle de redirecció ni rep un 403 després d'un login correcte  
**But** un compte amb Professorat + Secretaria conserva la destinació i els accessos normals de Professorat.

### Escenari 3: accedir només a l'operativa per mòdul de Secretaria

**Given** un usuari autenticat amb rol Secretaria  
**When** consulta el llistat per mòdul, genera'n el PDF o confirma el registre de les peticions acceptades incloses en el PDF  
**Then** pot executar les operacions de consulta i PDF ja previstes  
**And** si confirma el registre, només les peticions incloses que continuen en `Acceptada` passen a `Acceptada (Convalidada)` en la intranet  
**And** queda registrada la persona responsable i la data segons el flux existent  
**And** l'acció no modifica dades directament en ITACA  
**But** no pot obrir el detall gestor d'una sol·licitud, resoldre peticions, aplicar regles automàtiques ni accedir a la configuració de Direcció.

### Escenari 4: aïllar Secretaria de les àrees generals d'altres rols

**Given** un usuari que només té Secretaria  
**When** intenta accedir directament a una ruta protegida per `role:profesor`, `role:direccion` o `role:administrador`  
**Then** l'accés és denegat pel servidor  
**And** el rol Secretaria no implica ni afegix cap d'aquests rols  
**But** un usuari amb Professorat + Secretaria manté els accessos de tots dos rols  
**And** un professor sense Secretaria no pot consultar el llistat per mòdul, generar-ne el PDF ni confirmar-ne el registre.

### Escenari 5: separar el càrrec de secretari del nou rol operatiu

**Given** existeix un DNI en `avisos.secretario`  
**When** el compte corresponent no té el factor Secretaria  
**Then** el DNI, per si mateix, no autoritza les rutes ni mostra l'entrada de menú de convalidacions  
**And** `avisos.secretario` continua disponible per als usos de signatures, documents o altres responsabilitats del càrrec  
**And** no s'afig automàticament el factor `53` a la persona configurada com a secretari de l'equip directiu  
**But** si eixa persona té el rol Direcció, conserva l'accés a les convalidacions per les autoritzacions pròpies de Direcció.

### Escenari 6: mostrar l'accés de convalidacions en el menú segons el rol

**Given** un usuari amb Secretaria o Direcció  
**When** es construïx el menú general  
**Then** veu l'accés al llistat de convalidacions per mòdul que li correspon  
**But** un professor sense Secretaria ni Direcció no el veu  
**And** afegir o retirar Secretaria actualitza la visibilitat de menú sense dependre del DNI configurat ni d'una cache obsoleta.

### Escenari 7: assignar i retirar Secretaria només des d'un flux autoritzat

**Given** Direcció o Administració edita el perfil d'un usuari  
**When** marca o desmarca el rol Secretaria i guarda els canvis  
**Then** el factor es combina o es lleva preservant els altres rols  
**And** el formulari mostra el nom del rol en valencià, castellà i anglés segons l'idioma actiu  
**But** una persona sense rol de Direcció o Administració no veu els controls de rols  
**And** si manipula la petició per afegir o llevar Secretaria, el backend conserva intactes els rols existents.

## Regles de negoci

- Secretaria és un factor de rol independent amb valor primer `53`; no és un àlies de Professorat ni de Direcció.
- El rol es guarda en el mateix compte de personal que usa l'autenticació actual; no es crea un model, una credencial ni un sistema d'identitat separat.
- `todos` continua sent implícit perquè el seu valor és `1`.
- Secretaria només autoritza les rutes operatives de convalidacions per mòdul definides en esta proposta i les rutes generals de `todos`; no concedix cap accés de `profesor`, `direccion` o `administrador`.
- Direcció conserva els permisos que ja té; la gestió del detall, les resolucions, la configuració i els XML continuen sent exclusius de Direcció/Administració segons les rutes i policies existents.
- L'acció de Secretaria de confirmar un PDF només marca en la intranet les peticions acceptades seleccionades pel filtre del servidor; no és una integració amb ITACA.
- L'autorització de menú i policy depén del factor de rol, no de comparar DNI amb `avisos.secretario`.
- La configuració `avisos.secretario` no s'elimina ni es canvia per als usos no autoritzatius que encara en depenen.
- La gestió del rol continua reservada a Direcció i Administració i protegida al servidor, incloses peticions manipulades.
- El factor Secretaria no s'assigna automàticament a partir del càrrec de secretari de l'equip directiu; Direcció o Administració l'assignen explícitament a les persones que fan l'operativa de Secretaria.

## Fitxers previsibles

- `config/roles.php` — registrar el nou factor `secretaria = 53` en les dues direccions de la taula de rols.
- `app/Policies/ConvalidacioPolicy.php` — autoritzar l'accés per mòdul i el registre per factor Secretaria o Direcció, i retirar l'autorització per DNI.
- `app/Application/Menu/MenuService.php` — mostrar l'accés de convalidacions segons el rol i invalidar/cachejar coherentment després d'un canvi de rols.
- `app/Providers/RouteServiceProvider.php`, `routes/convalidacions-moduls.php` i `routes/todos.php` — revisats; les rutes existents ja separen el guard d'autenticació de professorat dels rols i no requerixen canvis.
- `app/Http/Controllers/Auth/LoginController.php` i `app/Http/Controllers/Auth/Profesor/LoginController.php` — decidir la destinació després del login i del primer login per a Secretaria sola, preservant les destinacions dels rols combinats.
- `app/Http/Controllers/Auth/PerfilController.php`, `resources/views/perfil/partials/roles.blade.php` i `resources/lang/{ca,es,en}/messages.php` — incloure el rol en el flux de gestió ja autoritzat i en les traduccions existents.
- `tests/Feature/ConvalidacioFlowTest.php`, `tests/Feature/AuthProfesorLoginControllerFeatureTest.php`, `tests/Feature/PerfilControllerTest.php` i `tests/Feature/RouteNameContractTest.php` — cobertura d'autorització, login, menú/rols i contracte de rutes.
- `specs/perfils.md` i `specs/convalidacions.md` — integrar els escenaris aprovats quan es complete la fase d'arxiu OpenSpec.

## Contractes de prova previstos

- Login normal i primer login de Secretaria sola, redirecció a la ruta autoritzada i combinació Professorat + Secretaria; comprovació de l'accés `todos` i de la codificació multiplicativa.
- Llistat, PDF GET i registre confirmat des del PDF; comprovació que només canvien les peticions acceptades filtrades.
- Denegació a rutes `role:profesor`, `role:direccion` i `role:administrador` per a Secretaria sola.
- Conservació dels accessos per a Professorat + Secretaria i denegació d'accés específic a un professor sense Secretaria.
- DNI de `avisos.secretario` sense rol no autoritza el llistat ni el menú, mantenint els usos de signatures/documents; el càrrec no rep automàticament Secretaria.
- Menu visible per Secretaria i Direcció, no visible per Professorat sense Secretaria; verificació d'invalidació de cache quan canvia el rol.
- Només Direcció i Administració poden assignar/retirar Secretaria; una petició manipulada no autoritzada no altera el rol.
- `php artisan route:cache` i `RouteNameContractTest` passen sense noms duplicats ni accés accidental a grups generals.

## Riscos i decisions a validar

- Les rutes de convalidacions carreguen el guard `profesor`, però no el middleware `role:profesor`; la policy és la frontera per a l'accés restringit de Secretaria. Esta separació s'ha preservat i s'han provat també les accions POST/PDF.
- El login ordinari i el primer login tenen punts de redirecció separats. Cal cobrir tots dos, així com l'accés a `/login` quan ja hi ha una sessió, per evitar 403 o bucles.
- El menú té cache per usuari; canviar el rol ha d'invalidar-la o fer que el rol es torne a comprovar abans de renderitzar l'accés.
- Afegir el rol a `config/roles.php` també el farà seleccionable en el formulari compartit. El backend ha de continuar autoritzant l'assignació exclusivament a Direcció/Administració; el rol s'ha d'assignar a les persones que realment fan les tasques de Secretaria.

Status: ready_for_review
