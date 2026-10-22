# Proposta: rol independent de Secretaria

- Estat: `ready_to_commit`
- Domini: perfils, autenticació i operativa de convalidacions
- Punt d'entrada: `config/roles.php`, autenticació del perfil de professorat i autorització del llistat per mòdul
- Branca revisada: `323-add-mvp-de-sollicitud-i-gestió-manual-de-convalidacions`

## Context revisat

Els comptes del personal s'autentiquen com a `Profesor` amb el camp `codigo`. La branca ja incorpora el factor `secretaria = 53`, el login i l'entrada de menú, i autoritza per rol el llistat per mòdul, el PDF i el registre de peticions com a `Acceptada (Convalidada)`. Eixe accés substituïx la identificació per DNI de `config('avisos.secretario')`; la configuració es conserva per als usos no autoritzatius.

La limitació actual és que Secretaria no pot obrir el llistat complet de sol·licituds ni el seu detall per revisar documents i gestionar els mòduls. El requisit ara és que puga operar des de «Equip Directiu / Convalida» amb els mateixos permisos de treball sobre sol·licituds que Direcció. El rol continua sent independent: això no ha de concedir accés general a les àrees de Direcció ni a la configuració administrativa de convalidacions.

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

### Escenari 3: consultar la vista completa de sol·licituds

**Given** un usuari autenticat amb rol Secretaria  
**When** obri «Equip Directiu / Convalida»
**Then** pot consultar el llistat de sol·licituds i obrir el detall complet de qualsevol sol·licitud, igual que Direcció
**And** veu per cada petició el mòdul, l'origen, la documentació, l'estat, les observacions i la traçabilitat disponibles per a Direcció
**And** pot continuar accedint al llistat per mòdul i al PDF operatiu.

### Escenari 4: revisar documentació i gestionar peticions com Direcció

**Given** un usuari autenticat amb rol Secretaria consulta una sol·licitud
**When** obri els documents aportats o executa una acció operativa de revisió d'una petició
**Then** pot descarregar la documentació autoritzada i realitzar les mateixes accions de revisió i resolució per mòdul que Direcció, incloent canviar l'estat, afegir observacions i registrar resultat o nota quan corresponga
**And** s'apliquen les mateixes validacions, transicions d'estat, traçabilitat i notificacions que quan actua Direcció
**And** pot executar les altres accions operatives que Direcció té en el llistat o detall de sol·licituds, inclosos els fluxos automàtics i el registre individual o en PDF quan estiguen disponibles
**And** les accions destructives, com eliminar una sol·licitud de prova, mantenen la confirmació i els avisos existents
**And** una petició terminal continua protegida per les mateixes restriccions que per a Direcció.

### Escenari 5: limitar Secretaria a la gestió de sol·licituds

**Given** un usuari que només té Secretaria
**When** intenta accedir a configuració de regles, gestió d'XML, catàlegs, control d'accés de proves, gestió de rols o altres àrees generals protegides per `role:direccion`, `role:administrador` o `role:profesor`
**Then** l'accés és denegat pel servidor
**And** Secretaria no implica ni afegix cap d'aquests rols
**But** un usuari amb Professorat + Secretaria manté els accessos de tots dos rols
**And** un professor sense Secretaria no obté accés a l'operativa de convalidacions reservada a Secretaria.

### Escenari 6: separar el càrrec de secretari del nou rol operatiu

**Given** existeix un DNI en `avisos.secretario`  
**When** el compte corresponent no té el factor Secretaria  
**Then** el DNI, per si mateix, no autoritza les rutes ni mostra l'entrada de menú de convalidacions  
**And** `avisos.secretario` continua disponible per als usos de signatures, documents o altres responsabilitats del càrrec  
**And** no s'afig automàticament el factor `53` a la persona configurada com a secretari de l'equip directiu  
**But** si eixa persona té el rol Direcció, conserva l'accés a les convalidacions per les autoritzacions pròpies de Direcció.

### Escenari 7: mostrar l'accés a les sol·licituds en el menú segons el rol

**Given** un usuari amb Secretaria o Direcció  
**When** es construïx el menú general  
**Then** veu l'accés «Equip Directiu / Convalida» a la vista de sol·licituds i a les operacions per mòdul autoritzades
**But** un professor sense Secretaria ni Direcció no el veu  
**And** afegir o retirar Secretaria actualitza la visibilitat de menú sense dependre del DNI configurat ni d'una cache obsoleta.

### Escenari 8: assignar i retirar Secretaria només des d'un flux autoritzat

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
- Secretaria autoritza la vista de sol·licituds de convalidació, el detall complet, la consulta de documents i les accions de revisió/resolució per mòdul disponibles per a Direcció, a més del llistat per mòdul, PDF i registre en la intranet.
- L'autorització de Secretaria queda limitada a l'operativa de sol·licituds: no concedix accés general a `profesor`, `direccion` o `administrador`, ni a configuració de regles, gestió d'XML/catàlegs, control d'accés de proves o gestió de rols.
- Direcció conserva els permisos que ja té; les eines de configuració de regles, XML/catàlegs i accés de proves continuen sent exclusives de Direcció, igual que la gestió de rols de Direcció/Administració.
- L'acció de Secretaria de confirmar un PDF només marca en la intranet les peticions acceptades seleccionades pel filtre del servidor; no és una integració amb ITACA.
- L'autorització de menú i policy depén del factor de rol, no de comparar DNI amb `avisos.secretario`.
- La configuració `avisos.secretario` no s'elimina ni es canvia per als usos no autoritzatius que encara en depenen.
- La gestió del rol continua reservada a Direcció i Administració i protegida al servidor, incloses peticions manipulades.
- El factor Secretaria no s'assigna automàticament a partir del càrrec de secretari de l'equip directiu; Direcció o Administració l'assignen explícitament a les persones que fan l'operativa de Secretaria.

## Fitxers previsibles

- `config/roles.php` — registrar el nou factor `secretaria = 53` en les dues direccions de la taula de rols.
- `app/Policies/ConvalidacioPolicy.php` — ampliar les autoritzacions de consulta, descàrrega de documents i resolució per al factor Secretaria, preservant el límit a operacions de sol·licituds i retirant l'autorització per DNI.
- `routes/direccion.php`, `routes/convalidacions-moduls.php`, `routes/convalidacions-sollicituds.php`, `app/Providers/RouteServiceProvider.php` i `app/Http/Controllers/DireccionConvalidacioController.php` — separar la protecció de la vista completa i les accions operatives de les rutes de configuració.
- `app/Application/Menu/MenuService.php` — mostrar l'accés de convalidacions segons el rol i invalidar/cachejar coherentment després d'un canvi de rols.
- `app/Providers/RouteServiceProvider.php` i `routes/todos.php` — revisats; les rutes existents ja separen el guard d'autenticació de professorat dels rols i no requerixen canvis.
- `app/Http/Controllers/Auth/LoginController.php` i `app/Http/Controllers/Auth/Profesor/LoginController.php` — decidir la destinació després del login i del primer login per a Secretaria sola, preservant les destinacions dels rols combinats.
- `app/Http/Controllers/Auth/PerfilController.php`, `resources/views/perfil/partials/roles.blade.php` i `resources/lang/{ca,es,en}/messages.php` — incloure el rol en el flux de gestió ja autoritzat i en les traduccions existents.
- `tests/Feature/ConvalidacioFlowTest.php`, `tests/Feature/AuthProfesorLoginControllerFeatureTest.php`, `tests/Feature/PerfilControllerTest.php` i `tests/Feature/RouteNameContractTest.php` — cobertura d'autorització, login, menú/rols i contracte de rutes.
- `specs/perfils.md` i `specs/convalidacions.md` — integrar els escenaris aprovats quan es complete la fase d'arxiu OpenSpec.

## Contractes de prova previstos

- Login normal i primer login de Secretaria sola, redirecció a la ruta autoritzada i combinació Professorat + Secretaria; comprovació de l'accés `todos` i de la codificació multiplicativa.
- Secretaria sola pot obrir el llistat complet, el detall d'una sol·licitud i els documents privats permesos; pot realitzar les accions de revisió/resolució de Direcció i rep els mateixos efectes de traçabilitat/notificació.
- Secretaria no pot accedir a regles, XML, catàlegs, bloqueig de proves ni gestió de rols; tampoc supera rutes generals `role:profesor`, `role:direccion` o `role:administrador`.
- Llistat, PDF GET i registre confirmat des del PDF; comprovació que només canvien les peticions acceptades filtrades.
- Denegació a rutes `role:profesor`, `role:direccion` i `role:administrador` per a Secretaria sola.
- Conservació dels accessos per a Professorat + Secretaria i denegació d'accés específic a un professor sense Secretaria.
- DNI de `avisos.secretario` sense rol no autoritza el llistat ni el menú, mantenint els usos de signatures/documents; el càrrec no rep automàticament Secretaria.
- Menu visible per Secretaria i Direcció, no visible per Professorat sense Secretaria; verificació d'invalidació de cache quan canvia el rol.
- Només Direcció i Administració poden assignar/retirar Secretaria; una petició manipulada no autoritzada no altera el rol.
- `php artisan route:cache` i `RouteNameContractTest` passen sense noms duplicats ni accés accidental a grups generals.

## Riscos i decisions a validar

- Les rutes de sol·licituds i configuració conviuen davall del prefix `direccion/convalidacions`. Cal autoritzar Secretaria acció per acció o amb una frontera equivalent, evitant que ampliar la vista completa li done accés accidental a regles, XML, catàlegs o control de proves.
- Les rutes de convalidacions carreguen el guard `profesor`, però no necessàriament el middleware `role:profesor`; la policy i les comprovacions del controlador han de ser la frontera per a les accions sensibles de Secretaria.
- El login ordinari i el primer login tenen punts de redirecció separats. Cal cobrir tots dos, així com l'accés a `/login` quan ja hi ha una sessió, per evitar 403 o bucles.
- El menú té cache per usuari; canviar el rol ha d'invalidar-la o fer que el rol es torne a comprovar abans de renderitzar l'accés.
- Afegir el rol a `config/roles.php` també el farà seleccionable en el formulari compartit. El backend ha de continuar autoritzant l'assignació exclusivament a Direcció/Administració; el rol s'ha d'assignar a les persones que realment fan les tasques de Secretaria.

Status: ready_to_commit
