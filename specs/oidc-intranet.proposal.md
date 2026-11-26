# Proposta futura: proveïdor OIDC de la Intranet

- Estat: idea pendent de revisió; no hi ha desenvolupament aprovat.
- Domini: autenticació i integracions SSO.
- Punt d'entrada previst: petició d'autorització OIDC iniciada per Open WebUI; les rutes concretes depenen de la solució triada.
- Abast inicial proposat: professorat actiu i un client Open WebUI registrat manualment.
- Fonts de context: `docs/agents/conventions.md`, `docs/agents/openspec.md`, `specs/sso-nm.proposal.md`, `config/auth.php` i `composer.json`.

## Objectiu

Permetre que Open WebUI delegue l'autenticació en la Intranet mitjançant OpenID Connect (OIDC), sense rebre la contrasenya de l'usuari. La Intranet actua com a proveïdor d'identitat i Open WebUI gestiona la seua sessió després del login.

Esta és una proposta de treball futura, no una decisió d'implementar ni una selecció definitiva de dependències. La primera versió no inclou alumnat, gestió gràfica de clients, migració del SSO de NM ni canvis en l'API existent.

## Flux funcional previst

1. L'usuari tria iniciar sessió amb la Intranet en Open WebUI.
2. Open WebUI inicia el flux OIDC i envia el navegador a la Intranet.
3. La Intranet reutilitza una sessió vàlida o demana el login existent; després comprova que l'usuari està autoritzat.
4. La Intranet retorna un codi d'autorització temporal i d'un sol ús al client registrat.
5. Open WebUI intercanvia el codi, valida la resposta d'identitat i crea o recupera la seua sessió local.

## Escenaris proposats

### 1. Accés amb sessió existent

**Given** una persona amb sessió vàlida, compte actiu i rol de professorat autoritzat en la Intranet
**And** Open WebUI està registrat com a client OIDC
**When** inicia sessió en Open WebUI mitjançant la Intranet
**Then** la Intranet completa l'autorització sense tornar a demanar la contrasenya
**And** Open WebUI valida la identitat i obri la seua pròpia sessió.

### 2. Accés sense sessió prèvia

**Given** una persona sense sessió vàlida en la Intranet
**When** inicia el login des d'Open WebUI
**Then** la Intranet li demana que s'identifique amb el login existent
**And** després d'un login correcte reprén la petició OIDC original
**And** només completa l'autorització si el compte està autoritzat.

### 3. Usuari no autoritzat

**Given** un compte inactiu o una persona que no compleix la política d'accés aprovada
**When** intenta iniciar sessió en Open WebUI
**Then** la Intranet denega l'autorització
**And** no emet una resposta d'identitat vàlida per a eixe login.

### 4. Client o retorn no autoritzat

**Given** una petició amb un client desconegut o una URL de retorn no registrada per a eixe client
**When** la Intranet la valida
**Then** la rebutja sense redirigir credencials o codis a una destinació no autoritzada.

### 5. Protecció del flux i del codi

**Given** una petició OIDC iniciada per un client autoritzat
**When** es completa l'autorització i s'intercanvia el codi
**Then** el codi és de vida curta, està vinculat a la petició i es pot consumir una sola vegada
**And** es validen els requisits del protocol aplicables, incloent-hi PKCE quan s'utilitze
**And** es rebutgen codis caducats, reutilitzats o intercanviats per un client incorrecte.

### 6. Validació de la identitat

**Given** que Open WebUI rep una resposta del proveïdor
**When** valida el token d'identitat
**Then** comprova la firma i els claims exigits pel protocol, incloent-hi emissor, audiència, caducitat i `nonce` quan corresponga
**And** rebutja la resposta si alguna comprovació falla.

### 7. Compatibilitat amb els accessos existents

**Given** els fluxos existents de login, Sanctum i SSO de NM
**When** s'incorpora el proveïdor OIDC
**Then** estos fluxos continuen funcionant segons els seus contractes actuals
**And** no es reutilitzen les credencials ni les claus privades de NM o de Programacions per defecte.

### 8. Baixa i sessió de l'aplicació

**Given** un professor que deixa d'estar autoritzat
**When** intenta iniciar un nou login OIDC
**Then** la Intranet denega la nova autorització
**And** el tractament de les sessions d'Open WebUI ja obertes seguix la política acordada abans de desenvolupar.

## Regles i límits proposats

- Usar el flux Authorization Code amb una implementació OIDC mantinguda; verificar el suport de PKCE S256 i exigir-lo quan ho permeta el client i la configuració acordada.
- Registrar explícitament clients i URL de retorn; comparar les URL segons els requisits del protocol i del paquet.
- Escollir un `sub` estable i no reutilitzable que no expose el DNI. Cal verificar la idoneïtat de `Profesor.codigo`; no es pressuposa que ho siga.
- Compartir només els claims necessaris. Un JWT firmat no està xifrat; no ha de contindre secrets.
- Tractar per separat autenticació i autorització: un compte identificat no rep automàticament privilegis d'administració en Open WebUI.
- No activar la fusió automàtica de comptes per correu sense una política explícita i garanties de verificació del correu.
- Mantindre les claus i credencials fora del repositori, amb procediments de rotació i recuperació.
- No registrar tokens, contrasenyes, secrets ni codis d'autorització en logs.
- La sessió de la Intranet i la d'Open WebUI són independents; no es promet tancament immediat de sessions existents sense implementar-lo i provar-lo.

## Pla de treball futur

1. **Tancar l'abast:** perfils, estat actiu, identificador, claims, política de comptes existents i efecte de les baixes. Confirmar versió i desplegament d'Open WebUI.
2. **Prova tècnica aïllada:** comparar un proveïdor existent amb Passport i una extensió OIDC; comprovar dependències, manteniment, seguretat, compatibilitat amb Laravel 13 i `lcobucci/jwt` 5, guarda `profesor`, sessions i coexistència amb Sanctum. Connectar un Open WebUI de prova.
3. **Decisió d'arquitectura:** seleccionar la solució segons l'evidència de la prova, o descartar el proveïdor integrat si una alternativa externa resulta més adequada. Documentar decisions, costos i límits.
4. **Contracte definitiu:** concretar fluxos suportats, PKCE, claims, TTL, clients, consentiment, claus, revocació i sessions; actualitzar i aprovar esta spec abans d'implementar.
5. **Implementació:** integrar el proveïdor aprovat, persistència, configuració, control d'accés i registre manual d'Open WebUI, sense canviar els fluxos actuals.
6. **Validació:** afegir proves de regressió i seguretat, executar-les i provar el login real amb Open WebUI en un entorn aïllat.
7. **Desplegament:** preparar migracions, secrets, claus, còpies, rotació, monitoratge i pla de reversió; activar primer amb un grup pilot.

Estimació inicial, no compromís: 1–3 dies per a la prova tècnica i 8–15 dies en total per a una primera versió limitada, inclosa eixa prova. La selecció del paquet i les decisions pendents poden modificar-la.

## Fitxers i components afectats previsibles

- `composer.json` i `composer.lock`: dependències seleccionades després de la prova tècnica.
- `config/auth.php` i nova configuració del proveïdor: integració de guards, clients i claus; cal preservar els guards existents.
- `routes/`, `app/Http/Controllers/Auth/` i `app/Services/Auth/`: integració del flux i política d'accés, segons el paquet triat.
- `app/Entities/Profesor.php` o adaptadors d'identitat: identificador i claims, si la solució ho requerix.
- `database/migrations/`: persistència que requerisca el proveïdor i els seus clients.
- `tests/Feature/`, `tests/Unit/` i, si escau, `tests/Browser/`: contracte OIDC, regressió i flux amb navegador.
- `docs/integrations/` i `.env.example`: configuració, operació i variables sense secrets.

Esta llista és orientativa; no autoritza canvis fins que s'aprove el contracte definitiu.

## Riscos i decisions pendents

- **Identitat:** verificar estabilitat i unicitat del subjecte i la qualitat dels correus; decidir com resoldre correus duplicats o canviants.
- **Abast de persones:** confirmar si els rols combinats i altres perfils de personal poden entrar; alumnat queda fora de la primera proposta.
- **Convivència:** Passport pot requerir canvis al model i als guards; comprovar col·lisions amb Sanctum i les rutes existents.
- **Dependència:** validar compatibilitat real, estat de manteniment, cobertura i possibles vulnerabilitats abans de seleccionar un paquet.
- **Sessions i baixes:** decidir la duració màxima de sessions, reautenticació i necessitat de tancament remot; no assumir revocació immediata.
- **Comptes existents:** decidir un procediment segur de vinculació i provar especialment els comptes administradors.
- **Disponibilitat:** si la Intranet falla, els nous logins dependents també poden fallar; definir accés administratiu de contingència a Open WebUI.
- **Operació:** decidir qui gestiona clients i secrets, rotació de claus, monitoratge, incidents i recuperació.

## Criteris per considerar completa la futura implementació

- Escenaris aprovats coberts per proves, incloent-hi casos de rebuig i regressió de l'autenticació existent.
- Prova d'extrem a extrem satisfactòria amb la versió concreta d'Open WebUI i compte autoritzat i no autoritzat.
- Configuració, claus, migracions, rotació, reversió i límits de sessió documentats i comprovats en preproducció.
- Revisió independent de seguretat i aprovació abans de producció.

Status: awaiting_approval
