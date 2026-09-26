# Assumptes particulars

Especificació del bounded context que gestiona els permisos retribuïts per assumptes particulars del professorat.

## Regles de negoci

- El curs es computa de l'1 de setembre al 31 de juliol, sense acumulació.
- Cada professor disposa de bosses independents de 3 dies lectius i 3 no lectius, prorratejades segons `fecha_ingreso` i `fecha_baja`.
- El saldo prorratejat conserva decimals, però només es poden consumir dies complets.
- Només les peticions autoritzades consumeixen saldo i contingent.
- Una petició pendent no crea cap `Falta`.
- Les peticions ordinàries es presenten entre 7 dies naturals i un mes abans; amb menys de 7 dies requerixen motivació excepcional.
- El pla d'activitats no es demana en el formulari actual, però el camp es conserva per a usos futurs.
- El calendari escolar de Direcció registra les excepcions: un laborable sense registre és lectiu, un dia marcat `no lectiu` usa la bossa no lectiva i un `festiu` es rebutja.
- Els dissabtes i diumenges no es poden demanar, encara que no tinguen registre al calendari.
- No es permeten dies lectius consecutius; divendres i dilluns compten com a consecutius.
- El contingent màxim és de 8 autoritzacions per dia, repartides proporcionalment entre els torns de la plantilla.
- L'autorització revalida calendari, saldo i contingent dins d'una transacció amb bloqueig.
- El professor només gestiona peticions pròpies; Direcció o Administració les resol.
- Direcció consulta les peticions pendents agrupades per data i pot filtrar-les per una data exacta.
- En obrir el panell de Direcció es mostra per defecte el seté dia natural posterior a hui; es pot seleccionar una altra data o mostrar-les totes.
- Dins d'un dia, la prioritat és: menys dies autoritzats en el curs, menys sessions lectives afectades, sol·licitud més antiga i ID més baix.
- El panell de Direcció mostra el contingent global i per torn, ocupat únicament per peticions autoritzades.
- Els canvis sobrevinguts de calendari, torn, saldo o contingent es mostren com a avisos abans de resoldre.
- La denegació requerix una motivació i no genera ni document de resolució ni `Falta`.
- L'autorització la pot efectuar qualsevol persona amb rol de Direcció només fins al seté dia natural posterior a hui, inclòs; requerix la rúbrica gràfica del professor i de la directora configurada.
- El PDF sempre mostra la rúbrica de la directora configurada, mentre que la petició registra en `resolta_per` la persona de Direcció que ha tramitat l'autorització.
- Una petició autoritzada genera un únic PDF d'una pàgina sobre el model oficial, amb les dues signatures, i una única `Falta` de dia complet.
- El document autoritzat queda arxivat en emmagatzematge privat i només el poden descarregar el professor titular, Direcció o Administració.
- El document autoritzat s'associa també a la `Falta` com a justificant i es conserva en el gestor documental quan es buiden les taules del curs.
- El PDF inclou la localitat editable del perfil, el cos i l'especialitat en camps separats i els dies consumits anteriors, sense comptar el dia sol·licitat.
- Direcció pot anul·lar una `Falta` autoritzada amb motiu només abans del tancament mensual; l'anul·lació allibera el dia d'assumptes particulars i elimina el PDF i la seua entrada documental.
- Una petició urgent confirmada avisa per correu la directora configurada i posa en còpia el cap d'estudis configurat, sense adjunts; la previsualització no envia cap avís.
- La denegació comunica el motiu al professor i l'autorització li envia un enllaç autenticat a la resolució, sense adjuntar el PDF.
- Cada transició genera com a màxim un registre de correu; una fallada d'enviament no desfà la petició i queda registrada per a reintents sense exposar dades sensibles als logs.
- Direcció consulta totes les autoritzacions del curs i identifica qui les ha tramitades.
- Una regularització representa una autorització prèvia externa, consumix saldo i queda diferenciada de les sol·licituds ordinàries.
- Les regularitzacions no creen `Falta`, PDF, entrada documental ni notificació; l'arxiu de curs les ignora.
- Només Direcció pot regularitzar dies ja gaudits del curs vigent, amb saldo disponible i sense cap registre previ del professor en la mateixa data.

## Escenaris

### ✅ Una petició vàlida queda pendent

**Given** un professor amb saldo i calendari vàlid
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

**Then** veu per defecte el seté dia natural posterior a hui i pot triar una altra data o mostrar-les totes agrupades cronològicament.

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

### ✅ Direcció pot autoritzar amb la rúbrica de la directora configurada

**Given** una petició pendent

**When** una persona amb rol de Direcció, encara que no siga la directora configurada, l'autoritza

**Then** el PDF porta la rúbrica de la directora configurada i `resolta_per` identifica la persona que ha tramitat l'autorització; Administració sense rol de Direcció no pot autoritzar.

### ✅ L'autorització genera un únic document amb les dues signatures

**Given** una petició pendent vàlida i les rúbriques gràfiques del professor i de la directora

**When** la directora l'autoritza

**Then** es genera i arxiva un únic PDF d'una pàgina sobre el model oficial amb les dues signatures i es crea una única `Falta` de dia complet.

### ✅ L'autorització es limita als pròxims set dies naturals

**Given** una petició pendent per al seté o el huité dia natural posterior a hui

**When** Direcció la consulta i intenta autoritzar-la

**Then** el seté dia és autoritzable i el huité no ho és ni des del botó ni directament des del servei; no es genera cap falta ni document parcial.

### ✅ Una autorització invàlida no deixa artefactes parcials

**Given** una petició que deixa de complir el calendari, el saldo, la consecutivitat o el contingent

**When** la directora intenta autoritzar-la

**Then** la transacció es cancel·la i no queda cap PDF ni cap `Falta` parcial.

### ✅ El document autoritzat té accés restringit

**Given** el document arxivat d'una petició autoritzada

**When** un usuari intenta descarregar-lo

**Then** només el professor titular, Direcció o Administració hi poden accedir.

### ✅ El pla d'activitats no es demana en la sol·licitud actual

**Given** un professor que demana un dia lectiu

**When** ompli el formulari sense pla d'activitats

**Then** pot presentar la petició i el camp llegat continua disponible en les dades.

### ✅ El document firmat entra en el circuit de faltes

**Given** una petició autoritzada amb resolució firmada

**When** el professor o Direcció consulta la falta associada

**Then** pot obrir el mateix PDF com a justificant ordinari sense duplicar el fitxer.

### ✅ L'arxiu documental sobreviu al canvi de curs

**Given** una resolució autoritzada i arxivada

**When** es buiden les taules temporals del curs

**Then** la resolució i la seua titularitat continuen disponibles en el gestor documental.

### ✅ L'anul·lació abans del tancament allibera el dia

**Given** una falta autoritzada d'assumptes particulars que encara no s'ha tancat mensualment

**When** Direcció l'anul·la indicant-ne el motiu

**Then** la petició queda cancel·lada, el dia torna al saldo i s'eliminen la falta, el PDF i la seua entrada documental.

### ✅ Una falta tancada mensualment no es pot anul·lar

**Given** una falta autoritzada ja inclosa en el tancament mensual

**When** Direcció intenta anul·lar-la

**Then** l'operació es rebutja i es conserven la falta, la petició i el PDF.

### ✅ Una petició urgent avisa Direcció només quan es confirma

**Given** una petició amb menys de set dies d'antelació i motivació excepcional

**When** el professor la previsualitza i després la presenta

**Then** la previsualització no envia res i la petició confirmada posa en cua un únic correu per a la directora configurada, amb el cap d'estudis configurat en còpia i sense adjunts.

### ✅ La denegació informa del motiu sense duplicats

**Given** una petició pendent

**When** Direcció la denega amb una motivació i intenta resoldre-la de nou

**Then** el professor rep l'avís motivat una sola vegada i el segon intent no genera cap correu nou.

### ✅ L'autorització comunica un enllaç protegit

**Given** una petició pendent amb una resolució firmada

**When** la directora l'autoritza

**Then** el professor rep un enllaç autenticat al document, sense cap PDF adjunt.

### ✅ Una fallada del correu no desfà la resolució

**Given** una petició denegada i un error SMTP o un destinatari sense adreça vàlida

**When** es processa l'avís

**Then** la denegació es conserva, l'error queda registrat sense dades sensibles i l'enviament fallit es pot reintentar.

### ✅ Direcció consulta l'històric d'autoritzacions

**Given** peticions autoritzades durant el curs actual

**When** Direcció obri el panell d'assumptes particulars

**Then** veu el professor, la data de gaudi, el tipus, l'origen, la data de resolució i la persona que l'ha tramitada.

### ✅ L'històric diferencia les regularitzacions

**Given** una autorització ordinària i una regularització històrica

**When** Direcció consulta l'històric

**Then** distingix els dos orígens i només veu l'enllaç de resolució quan existix un PDF.

### ✅ Direcció registra un dia ja gaudit

**Given** un professor amb saldo i un dia passat del curs vigent

**When** Direcció confirma la regularització com a lectiva o no lectiva

**Then** es crea una autorització històrica, s'identifica qui l'ha introduïda i el dia consumix saldo.

### ✅ La regularització no crea artefactes ordinaris

**Given** un dia autoritzat prèviament fora de la intranet

**When** Direcció el regularitza

**Then** no es crea cap `Falta`, PDF, entrada documental ni notificació.

### ✅ No es dupliquen dies regularitzats

**Given** que un professor ja té una petició o regularització en una data

**When** Direcció intenta registrar de nou el mateix dia

**Then** l'operació es rebutja sense modificar el saldo.

### ✅ La regularització respecta el saldo

**Given** un professor sense un dia complet disponible del tipus seleccionat

**When** Direcció intenta regularitzar-lo

**Then** l'operació es rebutja i no es crea cap registre.

### ✅ La regularització està restringida a Direcció

**Given** un professor ordinari o una persona administradora sense rol de Direcció

**When** intenta executar una regularització

**Then** rep una denegació d'accés i no es modifica cap dada.

### ✅ Només es regularitzen dies ja gaudits del curs vigent

**Given** una data futura o fora del curs vigent

**When** Direcció intenta regularitzar-la

**Then** l'operació es rebutja amb un missatge específic.
