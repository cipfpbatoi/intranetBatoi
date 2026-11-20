# Inventari

## Espais

### Creació restringida ✅

**Given** una persona autenticada que accedix a la gestió d'espais
**When** intenta crear un espai nou
**Then** només una persona amb rol de Direcció pot completar l'alta.

**Given** una persona amb rol d'Administració o Manteniment
**When** envia directament la petició de creació d'un espai
**Then** el sistema la denega i no crea cap espai.

**Given** una persona amb rol d'Administració o Manteniment autenticada a l'API
**When** envia `POST /api/espacio`
**Then** el sistema respon amb un 403 i no crea cap espai.

### Formulari d'alta ✅

**Given** una persona de Direcció que obri el llistat d'espais
**When** es renderitza el formulari modal d'alta
**Then** usa la ruta `POST /espacio/create` i la pantalla no genera cap error de ruta.

**Given** una persona de Direcció que edita un espai
**When** guarda els canvis
**Then** la petició usa `PUT /espacio/{espacio}/edit`.
