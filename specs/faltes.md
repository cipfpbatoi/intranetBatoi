# Faltes del professorat

## Esborrat i actualització del panell de Direcció

### Regles de negoci

- Direcció pot esborrar una falta en qualsevol estat mentre no s'haja imprés l'informe mensual que la inclou. L'estat `4` o un `idDocumento` informat identifiquen una falta impresa i en bloquegen l'esborrat.
- L'esborrat de Direcció requerix confirmació i un motiu no buit de fins a 1000 caràcters; el registre de l'operació conserva la identitat, el motiu i la data.
- Els permisos i la impressió es revaliden en confirmar, i el servei comprova la marca d'impressió dins de la transacció amb bloqueig de la falta.
- Si la falta correspon a una concessió d'assumptes particulars, es cancel·la la petició, es recuperen saldo i contingent i s'eliminen la falta, el PDF i l'entrada documental associada.
- Esborrar una baixa de llarga durada no tramita l'alta del professor ni modifica la seua data de baixa.
- L'acció «Actualitzar dades» consulta de nou les faltes, el professorat, les accions disponibles i els recomptes dels estats, mantenint els filtres seleccionats.
- Els altres rols no obtenen permisos nous per a esborrar autoritzacions, baixes llargues ni faltes impreses.

### Direcció esborra una falta abans d'imprimir

**Given** una falta no inclosa en un informe mensual imprés, independentment del seu estat

**When** Direcció selecciona la paperera i confirma amb un motiu vàlid

**Then** s'elimina la falta i el document associat, es registra l'operació i s'actualitza el llistat.

### La confirmació requerix un motiu i es pot cancel·lar

**Given** el formulari d'esborrat obert

**When** Direcció intenta confirmar amb un motiu buit o format només per espais, o cancel·la el formulari

**Then** confirmar sense motiu mostra un error, i cancel·lar no modifica ni la falta ni el document.

### Una falta impresa no es pot esborrar

**Given** una falta amb estat imprés o vinculada a l'informe mensual, inclosa una baixa llarga

**When** es consulta el llistat o s'intenta executar l'esborrat directament

**Then** no apareix la paperera i el servidor rebutja l'operació sense modificar dades.

### La impressió sobrevinguda impedix confirmar

**Given** una confirmació oberta abans d'imprimir l'informe mensual

**When** s'imprimix l'informe i després es confirma l'esborrat

**Then** es rebutja l'operació i es conserva la falta.

### L'esborrat respecta les concessions i les baixes llargues

**Given** una falta d'assumptes particulars autoritzada o una baixa de llarga durada no impresa

**When** Direcció confirma l'esborrat

**Then** en assumptes particulars es cancel·la la concessió i es recupera el saldo sense deixar artefactes, i en una baixa llarga no es modifica la situació laboral del professor.

### L'esborrat revalida els permisos

**Given** una confirmació oberta amb una identitat de Direcció

**When** una identitat sense eixe rol intenta confirmar l'esborrat d'una falta autoritzada

**Then** es denega l'accés i es conserva la falta.

### Actualitzar dades conserva els filtres

**Given** filtres de professorat i estat seleccionats, i canvis externs en les faltes o el professorat

**When** es prem «Actualitzar dades»

**Then** es mantenen els filtres i es mostren les faltes, les opcions de professorat, els recomptes i les accions disponibles actualitzades.
