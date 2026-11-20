# SSO de professorat per a NM

## Contracte

NM inicia el flux redirigint el navegador a:

```text
GET https://intranet.cipfpbatoi.es/sso/nm/login?state=<valor-opac>
```

La Intranet autentica exclusivament professorat actiu i retorna un formulari automàtic:

```text
POST https://nm.cipfpbatoi.es/login
Content-Type: application/x-www-form-urlencoded

token=<jwt>
state=<valor-opac-original>
```

La URL de retorn és fixa. No s'admet `redirect_uri` ni el JWT en query string.

## JWT

- Firma: `RS256`.
- Tipus: `nm-login+jwt`.
- Emissor: `https://intranet.cipfpbatoi.es`.
- Audiència: `nm.cipfpbatoi.es`.
- Caducitat màxima: 120 segons.
- Claims obligatoris: `iss`, `aud`, `sub`, `iat`, `nbf`, `exp`, `jti`, `token_use`, `name`, `surnames`, `email`, `role` i `version`.
- `sub` és `Profesor.codigo` convertit a text; no s'envia el DNI.
- `token_use` ha de ser `nm_sso` i `role` ha de ser `profesor`.

NM ha de fixar `RS256` en el validador, comprovar tots els claims anteriors i rebutjar un `jti` ja consumit fins que passe `exp`. Es recomana una tolerància temporal màxima de 30 segons.

## State i cookies

NM genera un `state` imprevisible amb almenys 128 bits d'entropia, el vincula al navegador que inicia el flux i el consumix una sola vegada. La Intranet el retorna sense modificar.

Com que el retorn és un POST entre dominis, una cookie `SameSite=Lax` pot no arribar a NM. NM ha d'usar `SameSite=None; Secure` per a la sessió implicada o un `state` firmat d'un sol ús que puga validar sense la cookie.

## Configuració de la Intranet

```dotenv
NM_SSO_CALLBACK=https://nm.cipfpbatoi.es/login
NM_SSO_ISSUER=https://intranet.cipfpbatoi.es
NM_SSO_AUDIENCE=nm.cipfpbatoi.es
NM_SSO_TTL=120
NM_SSO_KEY_ID=nm-2026-01
NM_SSO_PRIVATE_KEY=storage/keys/nm-sso-private.pem
NM_SSO_PUBLIC_KEY=storage/keys/nm-sso-public.pem
NM_SSO_PASSPHRASE=<secret-fora-del-repositori>
```

## Generació del parell RSA en producció

Les claus es generen en `intranet.cipfpbatoi.es`; la privada no ix del servidor:

```bash
install -d -m 700 storage/keys
openssl genpkey -algorithm RSA -aes-256-cbc -pkeyopt rsa_keygen_bits:3072 -out storage/keys/nm-sso-private.pem
openssl pkey -in storage/keys/nm-sso-private.pem -pubout -out storage/keys/nm-sso-public.pem
chmod 600 storage/keys/nm-sso-private.pem
chmod 644 storage/keys/nm-sso-public.pem
```

L'empremta que es comunica per un segon canal s'obté amb:

```bash
openssl pkey -pubin -in storage/keys/nm-sso-public.pem -outform DER | openssl dgst -sha256
```

Després de configurar l'entorn, cal reconstruir la cache de configuració i provar l'emissió abans d'activar l'enllaç en NM.

## Lliurament i rotació

S'envien a l'encarregat de NM només:

- `nm-sso-public.pem`.
- El `kid` actiu.
- L'empremta SHA-256 de la clau pública, verificada per un segon canal.

Per rotar, NM instal·la primer la clau pública nova, la Intranet canvia a un `kid` nou i la clau anterior es retira quan ja no quede cap token vigent.
