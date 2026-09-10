# Assumptes particulars

Especificació del bounded context que gestiona els permisos retribuïts per assumptes particulars del professorat.

## Regles de negoci

- El curs es computa de l'1 de setembre al 31 de juliol, sense acumulació.
- Cada professor disposa de bosses independents de 3 dies lectius i 3 no lectius, prorratejades segons `fecha_ingreso` i `fecha_baja`.
- El saldo prorratejat conserva decimals, però només es poden consumir dies complets.
- Només les peticions autoritzades consumeixen saldo i contingent.
- Una petició pendent no crea cap `Falta`.
- Les peticions ordinàries es presenten entre 7 dies naturals i un mes abans; amb menys de 7 dies requerixen motivació excepcional.
- Els dies lectius requerixen un pla d'activitats.
- El calendari escolar de Direcció determina els dies lectius, festius, avaluacions, exàmens i períodes exclosos.
- No es permeten dies lectius consecutius; divendres i dilluns compten com a consecutius.
- El contingent màxim és de 8 autoritzacions per dia, repartides proporcionalment entre els torns de la plantilla.
- L'autorització revalida calendari, saldo i contingent dins d'una transacció amb bloqueig.
- El professor només gestiona peticions pròpies; Direcció o Administració les resol.

## Escenaris

### ✅ Una petició vàlida queda pendent

**Given** un professor amb saldo, calendari vàlid i pla d'activitats quan correspon  
**When** crea una petició  
**Then** queda pendent i no genera cap `Falta`.

### ✅ Una petició excepcional requerix motivació

**Given** una data de gaudi amb menys de 7 dies naturals d'antelació  
**When** el professor presenta la petició sense motivació excepcional  
**Then** es rebutja amb un motiu específic.

### ✅ Les dates excloses es rebutgen

**Given** una data festiva, d'avaluació, d'examen o dins d'un període exclòs del calendari  
**When** es presenta la petició  
**Then** es rebutja indicant la restricció concreta.

### ✅ Els dies lectius consecutius es rebutgen

**Given** un permís lectiu autoritzat  
**When** es demana el dia lectiu immediat anterior o posterior  
**Then** es rebutja, inclòs el cas divendres-dilluns.

### ✅ Només les autoritzacions consumixen saldo

**Given** peticions pendents, denegades o cancel·lades  
**When** es calcula el saldo del curs  
**Then** només es resten les peticions autoritzades del mateix tipus.

### ✅ El saldo es prorrateja segons el nomenament

**Given** un professor que no està nomenat durant tot el curs  
**When** es calcula el límit lectiu o no lectiu  
**Then** els 3 dies es prorrategen segons la part efectiva del curs i es conserven els decimals.

### ✅ El contingent diari no supera huit places

**Given** peticions d'un mateix dia distribuïdes per torn  
**When** Direcció les autoritza  
**Then** no se superen huit autoritzacions ni la quota proporcional del torn.

### ✅ Les operacions estan protegides per policy

**Given** un usuari autenticat  
**When** consulta, modifica, cancel·la o resol una petició  
**Then** el professor només gestiona les pròpies pendents i únicament Direcció o Administració pot resoldre-les.
