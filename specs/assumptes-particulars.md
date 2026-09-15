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
- El calendari escolar de Direcció registra les excepcions: un laborable sense registre és lectiu, un dia marcat `no lectiu` usa la bossa no lectiva i un `festiu` es rebutja.
- Els dissabtes i diumenges no es poden demanar, encara que no tinguen registre al calendari.
- No es permeten dies lectius consecutius; divendres i dilluns compten com a consecutius.
- El contingent màxim és de 8 autoritzacions per dia, repartides proporcionalment entre els torns de la plantilla.
- L'autorització revalida calendari, saldo i contingent dins d'una transacció amb bloqueig.
- El professor només gestiona peticions pròpies; Direcció o Administració les resol.
- Direcció consulta les peticions pendents agrupades per data i pot filtrar-les per una data exacta.
- Dins d'un dia, la prioritat és: menys dies autoritzats en el curs, menys sessions lectives afectades, sol·licitud més antiga i ID més baix.
- El panell de Direcció mostra el contingent global i per torn, ocupat únicament per peticions autoritzades.
- Els canvis sobrevinguts de calendari, torn, saldo o contingent es mostren com a avisos abans de resoldre.
- La denegació requerix una motivació i no genera ni document de resolució ni `Falta`.
- L'autorització només la pot efectuar la persona configurada com a directora i requerix la rúbrica gràfica del professor i de la directora.
- Una petició autoritzada genera un únic PDF d'una pàgina sobre el model oficial, amb les dues signatures, i una única `Falta` de dia complet.
- El document autoritzat queda arxivat en emmagatzematge privat i només el poden descarregar el professor titular, Direcció o Administració.

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

### ✅ El calendari dispers classifica les dates

**Given** un calendari on només estan marcats els dies no lectius, els festius i els esdeveniments especials

**When** es valida una data sense registre

**Then** es considera lectiva si és laborable i es rebutja si cau en cap de setmana.

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

### ✅ Direcció consulta les peticions agrupades i filtrades per data

**Given** peticions pendents en diferents dates

**When** Direcció obri el panell o selecciona una data

**Then** les veu agrupades cronològicament o limitades a la data exacta seleccionada.

### ✅ La prioritat combina dies gaudits, hores i antiguitat

**Given** diverses peticions pendents per al mateix dia

**When** es calcula l'ordre del panell

**Then** apareixen primer les de menys dies autoritzats, menys sessions lectives, més antigues i amb ID més baix.

### ✅ El panell mostra torn, hores i contingent

**Given** l'horari vigent i les autoritzacions d'una data

**When** Direcció consulta el grup del dia

**Then** veu el torn actual, totes les sessions lectives afectades i les places totals, ocupades i disponibles per torn.

### ✅ Les incompatibilitats sobrevingudes són visibles

**Given** una petició pendent que ja no complix calendari, torn, saldo o contingent

**When** es prepara el panell

**Then** Direcció veu els avisos i el panell no resol encara la petició.

### ✅ Una denegació motivada no genera document ni absència

**Given** una petició pendent i un membre de Direcció o Administració

**When** la denega indicant-ne el motiu

**Then** la petició queda denegada sense generar cap PDF ni cap `Falta`.

### ✅ La sol·licitud requerix la rúbrica del professor

**Given** un professor que no té una rúbrica gràfica configurada

**When** intenta presentar una petició

**Then** la petició es rebutja i se li indica que configure la firma en el seu perfil.

### ✅ Només la directora configurada pot autoritzar

**Given** una petició pendent

**When** un usuari de Direcció o Administració que no és la directora configurada intenta autoritzar-la

**Then** l'operació es rebutja sense generar document ni absència.

### ✅ L'autorització genera un únic document amb les dues signatures

**Given** una petició pendent vàlida i les rúbriques gràfiques del professor i de la directora

**When** la directora l'autoritza

**Then** es genera i arxiva un únic PDF d'una pàgina sobre el model oficial amb les dues signatures i es crea una única `Falta` de dia complet.

### ✅ Una autorització invàlida no deixa artefactes parcials

**Given** una petició que deixa de complir el calendari, el saldo, la consecutivitat o el contingent

**When** la directora intenta autoritzar-la

**Then** la transacció es cancel·la i no queda cap PDF ni cap `Falta` parcial.

### ✅ El document autoritzat té accés restringit

**Given** el document arxivat d'una petició autoritzada

**When** un usuari intenta descarregar-lo

**Then** només el professor titular, Direcció o Administració hi poden accedir.
