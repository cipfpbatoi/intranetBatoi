# Proposta: SSO de professorat per a nm.cipfpbatoi.es

- Estat: `approved`
- Domini: autenticació i integracions SSO
- Punt d'entrada: `GET https://intranet.cipfpbatoi.es/sso/nm/login?state=<valor-opac>`
- Retorn autoritzat: `POST https://nm.cipfpbatoi.es/login`
- Abast: autenticació unidireccional de professorat des de la Intranet cap a `nm.cipfpbatoi.es`

## Contracte proposat

### Petició d'inici

`nm.cipfpbatoi.es` genera un `state` aleatori d'un sol ús, el vincula a la sessió del navegador i redirigix l'usuari a:

```text
GET https://intranet.cipfpbatoi.es/sso/nm/login?state=<valor-opac>
```

`state` és obligatori, ha de contindre almenys 128 bits d'entropia i la Intranet el retorna exactament, sense interpretar-lo ni modificar-lo.

### Resposta d'autenticació

La Intranet respon amb una pàgina transitòria que envia automàticament un formulari:

```text
POST https://nm.cipfpbatoi.es/login
Content-Type: application/x-www-form-urlencoded

token=<jwt-signat>
state=<mateix-valor-rebut>
```

La URL de retorn és fixa i no es pot substituir amb cap `redirect_uri` rebut de la petició.

### Capçalera JWT

- `alg`: `RS256`
- `typ`: `nm-login+jwt`
- `kid`: identificador versionat de la clau, inicialment `nm-2026-01`

### Claims JWT

- `iss`: `https://intranet.cipfpbatoi.es`
- `aud`: `nm.cipfpbatoi.es`
- `sub`: codi intern del professor (`Profesor.codigo`) convertit a text
- `iat`: instant d'emissió
- `nbf`: mateix instant que `iat`
- `exp`: `iat + 120` segons
- `jti`: identificador aleatori únic del token
- `token_use`: `nm_sso`
- `name`: nom del professor
- `surnames`: cognoms del professor
- `email`: correu del professor
- `role`: valor fix `profesor`
- `version`: `1`

No s'inclou el DNI. Si `nm.cipfpbatoi.es` necessita un identificador diferent de `Profesor.codigo`, cal acordar-lo explícitament abans d'implementar.

## Escenaris

### Escenari 1: iniciar SSO amb una sessió vàlida de professorat

**Given** una persona activa, autenticada amb el guard `profesor` i amb el factor de rol Professorat
**And** `nm.cipfpbatoi.es` envia un `state` vàlid
**When** obri l'endpoint `GET /sso/nm/login`
**Then** la Intranet emet un JWT destinat exclusivament a `nm.cipfpbatoi.es`
**And** mostra una resposta transitòria que envia el JWT per POST a la URL de retorn fixa.

### Escenari 2: autenticar abans d'emetre el token

**Given** una persona sense sessió autenticada en la Intranet
**When** accedix a l'endpoint amb un `state` vàlid
**Then** és redirigida al login existent de professorat
**And** la URL SSO completa, inclòs el `state`, es conserva com a destinació prevista
**And** després d'un login correcte es reprén el flux SSO sense perdre ni canviar el `state`.

### Escenari 3: limitar l'accés a professorat actiu

**Given** una sessió d'alumnat, una persona inactiva o un compte de personal sense el factor Professorat
**When** intenta usar l'endpoint SSO
**Then** la Intranet denega la petició sense emetre cap JWT
**And** el privilegi global d'administració no substituïx el requisit de tindre el factor Professorat
**But** una persona amb Professorat combinat amb altres rols sí que pot usar-lo.

### Escenari 4: emetre un JWT curt i específic per a nm

**Given** un professor autoritzat
**When** la Intranet genera l'assercí SSO
**Then** la firma usa exclusivament `RS256` i la clau privada dedicada a `nm`
**And** el token conté exactament l'`iss`, l'`aud`, el tipus i els claims definits en este contracte
**And** caduca 120 segons després de l'emissió
**And** no reutilitza les claus ni la configuració del JWT de Programacions.

### Escenari 5: conservar i validar state

**Given** `nm.cipfpbatoi.es` ha generat i guardat un `state` aleatori d'un sol ús
**When** la Intranet completa l'autenticació
**Then** retorna exactament el mateix `state` en el cos POST
**And** `nm.cipfpbatoi.es` compara el valor amb el vinculat a la sessió que va iniciar el flux
**And** consumix el `state` una sola vegada
**But** si falta, és invàlid, no coincidix o ja s'ha consumit, el login es rebutja.

### Escenari 6: validar i consumir el JWT en nm

**Given** `nm.cipfpbatoi.es` rep un `token` i un `state` vàlid
**When** valida la resposta SSO
**Then** exigix la firma amb la clau pública dedicada i l'algorisme exacte `RS256`
**And** valida estrictament `typ`, `kid`, `iss`, `aud`, `sub`, `iat`, `nbf`, `exp`, `jti` i `token_use`
**And** rebutja tokens caducats, prematurs, d'una altra audiència o d'un altre tipus
**And** impedix reutilitzar el mateix `jti` fins que haja passat `exp`
**And** pot aplicar una tolerància màxima de 30 segons per desajust de rellotge.

### Escenari 7: evitar filtracions i redireccions obertes

**Given** la Intranet ha emés una resposta SSO
**When** el navegador envia el formulari a `nm.cipfpbatoi.es`
**Then** el JWT no apareix en la query string, l'historial ni els logs d'accés normals
**And** la resposta usa `Cache-Control: no-store`, `Referrer-Policy: no-referrer` i una política que només permet enviar el formulari al retorn configurat
**And** ni el JWT ni la clau privada s'escriuen en logs
**And** cap paràmetre de la petició pot canviar la destinació del POST.

## Regles de negoci

- La integració és exclusiva per a professorat actiu; no autentica alumnat ni personal sense el factor Professorat.
- La duració del JWT és de 120 segons i no crea cap sessió llarga en la Intranet; `nm.cipfpbatoi.es` gestiona la seua pròpia sessió després del login.
- El retorn es fa per POST amb els camps `token` i `state`; no s'admet retorn per query string.
- `state` és obligatori, opac, d'un sol ús i responsabilitat de `nm.cipfpbatoi.es` generar-lo, vincular-lo a la sessió i validar-lo.
- La URL de retorn és una llista blanca d'un sol valor: `https://nm.cipfpbatoi.es/login`.
- La firma usa `RS256` amb un parell RSA dedicat a esta integració. La clau privada no abandona `intranet.cipfpbatoi.es`.
- Només la clau pública i la seua empremta SHA-256 es compartixen amb l'altra aplicació.
- Les claus SSO de `nm` no reutilitzen `JWT_PRIVATE_KEY`/`JWT_PUBLIC_KEY`, perquè eixes variables ja donen servei al JWT de Programacions.
- El JWT és firmat, no xifrat: els claims no han de contindre secrets ni més dades personals de les necessàries.
- El consumidor valida l'algorisme configurat; no confia en l'`alg` proposat pel mateix token.
- Els rellotges dels dos servidors han d'estar sincronitzats per NTP.

## Tasques proposades

1. **Tancar el contracte amb `nm`**: confirmar endpoint, noms de camps POST, `alg`, `typ`, `kid`, `iss`, `aud`, TTL, claims i l'ús de `Profesor.codigo` com a `sub`.
2. **Separar la configuració**: crear configuració pròpia per a `NM_SSO_CALLBACK`, `NM_SSO_ISSUER`, `NM_SSO_AUDIENCE`, `NM_SSO_TTL`, `NM_SSO_KEY_ID`, `NM_SSO_PRIVATE_KEY`, `NM_SSO_PUBLIC_KEY` i passphrase, sense secrets reals al repositori.
3. **Implementar l'emissor**: encapsular la construcció del JWT amb `lcobucci/jwt`, `RS256`, claims mínims i claus dedicades.
4. **Implementar l'endpoint protegit**: validar `state`, sessió, activitat i factor Professorat; conservar la destinació durant el login i denegar la resta de perfils.
5. **Implementar el retorn POST**: vista transitòria amb formulari autoenviat, botó manual de reserva i capçaleres de no-cache, no-referrer i restricció de `form-action`.
6. **Cobrir el contracte amb tests**: firma i claims, TTL, `state`, retorn POST, invitats, alumnat, comptes inactius, administració sense Professorat, URL fixa i absència de tokens en logs/URL.
7. **Preparar el desplegament**: generar en producció un parell RSA d'almenys 3072 bits, protegir la privada amb permisos mínims, configurar la passphrase fora del repositori i verificar la càrrega amb config cache.
8. **Fer el lliurament a `nm`**: enviar per correu només la clau pública PEM, el `kid` i l'empremta SHA-256; confirmar l'empremta per un segon canal i provar validació, expiració, `state` i reús de `jti` abans d'activar-ho.

## Fitxers afectats previstos

- `config/sso.php` — configuració dedicada de la integració, separada del JWT existent.
- `.env.example` — noms de variables sense claus ni passphrases reals.
- `routes/profesor.php` — ruta amb nom per a `GET /sso/nm/login`.
- `app/Http/Requests/Auth/Sso/NmSsoRequest.php` — validació de `state` i autorització estricta de professorat actiu.
- `app/Http/Controllers/Auth/Sso/NmSsoController.php` — orquestració del flux i resposta POST.
- `app/Services/Auth/NmSsoTokenService.php` — emissió del JWT `RS256`.
- `resources/views/auth/sso/post.blade.php` — formulari transitori cap al retorn fix.
- `tests/Unit/Services/Auth/NmSsoTokenServiceTest.php` — contracte criptogràfic, claims i caducitat.
- `tests/Feature/Auth/NmSsoControllerTest.php` — autenticació, autorització, `state`, POST i capçaleres.
- `tests/Feature/RouteNameContractTest.php` — absència de noms duplicats i compatibilitat amb `route:cache`.
- `docs/integrations/nm-sso.md` — contracte de consum, desplegament, rotació i verificació de la clau pública.

## Riscos i decisions pendents

- **Identificador del subjecte**: es proposa `Profesor.codigo` per evitar enviar el DNI. Cal confirmar que `nm` el pot usar com a identificador estable i que no necessita una altra dada.
- **POST i cookies `SameSite`**: un POST entre dominis no envia normalment una cookie de sessió `SameSite=Lax`. `nm` haurà d'usar una sessió compatible amb este retorn (`SameSite=None; Secure`) o un `state` firmat d'un sol ús que puga validar sense dependre d'eixa cookie.
- **Reús del token**: la caducitat curta limita el risc, però no substituïx el control de `jti`; el consumidor ha de registrar-lo temporalment i rebutjar replays.
- **Autenticació exacta**: el middleware `role:profesor` permet administració com a excepció global. L'endpoint necessita una comprovació addicional del factor Professorat per complir l'abast.
- **Separació de claus**: reutilitzar les claus actuals permetria confondre tokens de Programacions i `nm`; cal un parell i un `typ` dedicats.
- **Distribució de la clau**: la clau pública no és secreta i es pot enviar per correu, però cal verificar-ne l'autenticitat mitjançant l'empremta per un segon canal.
- **Rotació**: `nm` ha de poder mantindre temporalment la clau anterior i la nova distingides per `kid` per evitar talls durant una rotació.
- **Rellotges**: amb una vida de 120 segons, una desviació temporal notable pot invalidar tokens legítims; cal NTP i una tolerància curta i explícita.

Status: approved
