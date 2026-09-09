# Perfils d'usuari

Especificació del comportament de seguretat en l'edició dels perfils de
professorat i alumnat.

## Regles de negoci

- Només Direcció i Administració poden gestionar rols.
- La protecció dels rols s'aplica sempre al servidor.
- Els controls de rols no es mostren a usuaris sense permisos.
- Una petició no autoritzada conserva el rol existent sense impedir
  l'actualització dels altres camps editables.

## Escenaris

### ✅ Usuari sense permisos no veu els controls de rols

**Given** un usuari autenticat sense rol de Direcció o Administració  
**When** obri un formulari de perfil  
**Then** no veu controls per modificar rols.

### ✅ Una petició manipulada no modifica el rol

**Given** un usuari autenticat sense permisos per gestionar rols  
**When** envia manualment el camp rol en una petició PUT del perfil  
**Then** el backend conserva els rols emmagatzemats.

### ✅ El flux autoritzat conserva la gestió de rols

**Given** un usuari de Direcció o Administració  
**When** modifica els rols d'un usuari mitjançant el flux de gestió autoritzat  
**Then** els rols nous es guarden correctament.

### ✅ Professorat i alumnat poden actualitzar la resta del perfil

**Given** un professor o un alumne autenticat sense permisos per gestionar rols  
**When** actualitza les dades ordinàries del seu perfil i manipula el camp rol  
**Then** es guarden les dades ordinàries i es conserva el rol anterior.
