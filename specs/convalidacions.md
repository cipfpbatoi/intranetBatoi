# Convalidacions — MVP de sol·licitud i gestió manual

Issue: #323
Status: ready_for_review

## Abast

Primera versió del procediment perquè l'alumnat presente una sol·licitud amb una o més peticions de convalidació de mòduls i perquè Direcció les revise i resolga manualment.

El circuit cobert és: crear la composició temporal de la sol·licitud, afegir-hi peticions, confirmar-ne el resum, tramitar-la, revisar cada petició, requerir una correcció documental o resoldre-la i consultar-ne el resultat.

No hi ha esborranys persistents: la capçalera i les peticions només es guarden quan l'alumne confirma «Tramitar sol·licitud».

Este domini és independent de les convalidacions o exempcions d'FCT i no reutilitza `FctConvalidacion`.

## Escenaris

### Escenari 1: consulta privada i inici d'una sol·licitud

Given un alumne autenticat
When obri el panell «Convalidar»
Then veu únicament les seues sol·licituds amb la data de tramitació, els mòduls inclosos, l'estat de cada petició i les observacions de Direcció que existisquen
And pot iniciar una «Nova sol·licitud»
And si intenta consultar o descarregar una sol·licitud, petició o document d'un altre alumne mitjançant un identificador manipulat, el sistema denega l'accés

### Escenari 2: composició d'una sol·licitud amb diversos mòduls

Given un alumne amb matrícula vigent que està preparant una nova sol·licitud
When afig dos o més mòduls destí vàlids mitjançant «Afegir mòdul a convalidar»
Then cada mòdul queda representat com una petició independent dins de la mateixa composició
And pot eliminar qualsevol petició abans de tramitar
And no pot afegir dues vegades el mateix mòdul destí
And abans de guardar veu un resum de totes les peticions incloses
And el formulari comença buit i només mostra les dades de la petició que està component, no una targeta completa per cada mòdul matriculat
And en afegir un altre mòdul el resum conserva les peticions anteriors i confirma visualment quantes n'hi ha
And en pantalles amples el compositor i el resum es mostren en paral·lel per mantindre visible el resultat de cada acció

### Escenari 3: petició basada en estudis del propi centre

Given un alumne que ha seleccionat un mòdul destí de la seua matrícula vigent
When indica que ha cursat al propi centre el mòdul o els estudis que aporta
Then pot seleccionar com a «Estudi previ» un dels cicles que consten en el seu historial acadèmic del centre
And el mòdul destí i el cicle origen queden identificats separadament
And pot afegir la petició sense document adjunt
And el sistema valida en backend que el destí pertany a la matrícula vigent i que el cicle origen consta realment en l'historial de l'alumne

### Escenari 4: petició basada en estudis externs o certificats

Given un alumne que ha seleccionat un mòdul destí de la seua matrícula vigent
When selecciona «Estudis o certificats acadèmics d'un altre centre», «Certificat d'escola oficial d'idiomes» o «Prevenció de riscos (LOGSE)»
Then ha d'acceptar la declaració responsable
And ha d'adjuntar un únic fitxer PDF, JPG, JPEG o PNG que complisca l'extensió, el tipus MIME i el límit de mida configurat
And si falta algun requisit o el tipus no és vàlid, el sistema no permet afegir ni tramitar la petició i mostra un error específic
And si tot és vàlid, la petició es pot afegir i el document queda destinat a emmagatzematge privat
And el selector d'estudi previ no es mostra en estos casos

### Escenari 5: titulació que s'ha de tramitar per Secretaria

Given un alumne que està afegint un mòdul a convalidar
When selecciona «Títol universitari o FP1/FP2»
Then el sistema informa que este tipus de convalidació s'ha de consultar amb Secretaria
And no crea ni afig cap petició a la sol·licitud
And no mostra ni exigix document adjunt ni declaració responsable

### Escenari 6: una petició oberta no es pot duplicar

Given un alumne que ja ha sol·licitat la convalidació d'un mòdul
When la petició anterior està en qualsevol estat diferent de `Denegada`
Then el mòdul no apareix entre els destins disponibles d'una nova sol·licitud
And el backend rebutja igualment un identificador manipulat que intente tornar-lo a sol·licitar
But si l'última petició aplicable està `Denegada`, l'alumne pot tornar a sol·licitar el mòdul
And si la petició es resol com a `Realitzada`, el mòdul continua bloquejat encara que la baixa de matrícula encara no s'haja sincronitzat

### Escenari 7: tramitació transaccional i idempotent

Given una composició amb una o més peticions vàlides
When l'alumne confirma «Tramitar sol·licitud»
Then es crea una única capçalera associada a l'alumne autenticat
And es creen totes les peticions associades inicialment en estat `En procés`
And l'operació és transaccional i no deixa dades parcials si falla qualsevol part
And un doble clic o un reintent de la mateixa tramitació no crea capçaleres ni peticions duplicades
And el sistema mostra una confirmació de la tramitació

### Escenari 8: revisió manual per Direcció

Given una persona amb perfil de Direcció i una petició que no està en estat terminal
When consulta la sol·licitud, descarrega un adjunt autoritzat o canvia manualment l'estat de la petició
Then pot veure l'alumne, el mòdul destí, l'origen, la data, l'estat, l'observació i la resta de peticions de la sol·licitud
And pot assignar qualsevol estat admés
And l'observació és obligatòria per a `Denegada`, `Revisar documentació` i `Aportar documentació original a Secretaria`
And es guarden en la petició l'última persona responsable, la data del canvi i l'observació
And el canvi no altera l'estat de les altres peticions de la mateixa sol·licitud
And el panell permet filtrar les sol·licituds pels estats i orígens de les peticions associades

### Escenari 9: correcció documental i petició terminal

Given una petició externa en estat `Revisar documentació`
When l'alumne consulta l'observació de Direcció, substituïx únicament el fitxer adjunt i confirma
Then el nou document queda en emmagatzematge privat
And només eixa petició torna a `En procés`
And no es poden modificar el mòdul destí, l'origen, el tipus ni altres dades ja tramitades
But si la petició està en estat `Realitzada`, tant l'alumne com Direcció només poden consultar-la i no poden modificar-ne l'estat, les dades ni el document

## Regles de negoci

### Model i composició

- Una sol·licitud correspon a un únic alumne i conté una o més peticions de convalidació.
- Cada petició correspon a un únic mòdul destí de la matrícula vigent de l'alumne.
- El mateix mòdul destí no es pot repetir dins d'una mateixa sol·licitud.
- Un alumne no pot crear una nova petició d'un mòdul si ja en té una anterior en qualsevol estat diferent de `Denegada`.
- `Denegada` és l'únic estat que torna a habilitar el mòdul per a una nova sol·licitud; `Realitzada` el manté bloquejat fins i tot abans que desaparega de la matrícula.
- El mòdul destí i, quan corresponga, el cicle d'estudi previ es validen en backend; no es confia en identificadors enviats pel navegador.
- La relació conceptual obligatòria és `1 sol·licitud : N peticions`, encara que la nomenclatura tècnica definitiva s'adapte als patrons del projecte.
- No es poden editar ni cancel·lar sol·licituds ja tramitades, llevat de la substitució documental expressament permesa.

### Orígens i documentació

- «Propi centre» requerix seleccionar com a «Estudi previ» un cicle cursat que conste en l'historial acadèmic de l'alumne i no exigix adjunt en l'MVP.
- Els orígens externs es presenten en tres opcions compactes: estudis o certificats acadèmics d'un altre centre, certificat d'EOI i prevenció de riscos LOGSE.
- Els tres orígens externs admesos exigixen la declaració «Declare que la informació aportada és original i que dispose dels originals en cas que se'm demanen» i un únic adjunt.
- La interfície mostra els formats admesos i el límit de mida configurat per l'aplicació.
- Els adjunts es guarden en emmagatzematge privat, mai en `public/` ni en un disc públic.
- La descàrrega passa sempre per autorització i només està disponible per a l'alumne propietari i per a Direcció.
- «Títol universitari o FP1/FP2» només mostra l'avís de Secretaria i no genera cap petició.
- El document i la declaració només es mostren per als orígens externs tramitables; no apareixen per a propi centre ni per a titulacions derivades a Secretaria.
- En substituir documentació, l'alumne només pot canviar el fitxer de la petició afectada.

### Estats i revisió

- L'estat pertany a cada petició, no a la capçalera de la sol·licitud.
- Els estats admesos són:
  1. `En procés`.
  2. `Realitzada`.
  3. `Realitzada pendent de revisió`.
  4. `Denegada`.
  5. `Revisar documentació`.
  6. `Aportar documentació original a Secretaria`.
- `En procés` és l'estat inicial de tota petició tramitada.
- `Realitzada` és terminal i de només consulta.
- Direcció pot canviar manualment qualsevol petició no terminal a un altre estat admés.
- L'observació és obligatòria en passar a `Denegada`, `Revisar documentació` o `Aportar documentació original a Secretaria`.
- Cada petició conserva, com a mínim, l'última persona que ha canviat l'estat, la data del canvi i l'observació.
- Una mateixa sol·licitud pot contindre peticions en estats diferents.

### Autorització i seguretat

- L'alumne només pot consultar, tramitar i corregir les seues pròpies sol·licituds i peticions.
- Direcció és l'únic perfil gestor de l'MVP.
- No es confia en IDs, estats, orígens, tipus ni permisos rebuts des del navegador sense validació al servidor.
- Els missatges, les observacions i els adjunts no exposen informació d'altres persones.

## Components previstos

- Migracions i entitats separades per a la capçalera i les peticions.
- Policy de propietat per a alumnat i accés gestor per a Direcció.
- Rutes d'alumnat en `routes/alumno.php` i rutes protegides en `routes/direccion.php`.
- Entrada «Convalidar» al menú de l'alumnat i panells de presentació, consulta i revisió.
- Emmagatzematge privat i descàrrega mitjançant un controlador amb autorització.
- Tests Unit i Feature per a propietat, autorització, validació, estats, adjunts, transaccionalitat, idempotència i contracte de rutes.

## Expressament posposat

- Càlcul automàtic d'equivalències i taules de correspondències.
- Tasques nocturnes i qualsevol processament automàtic.
- Aprovació o altres accions massives.
- OCR, IA, models en la DGX Spark i fine-tuning.
- Notificacions de panell o correu.
- Rol o permisos específics de Secretaria.
- Historial complet de transicions.
- Múltiples documents per petició i versionat documental.
- Edició o cancel·lació d'una sol·licitud ja tramitada.
- Text normatiu definitiu i enllaços oficials sobre titulacions universitàries.
- Automatització específica per a `Realitzada pendent de revisió`.

## Criteri global de finalització

- Tots els escenaris anteriors disposen de tests automatitzats.
- La creació de la capçalera i de totes les peticions és transaccional i idempotent davant reintents.
- Cap alumne pot consultar o descarregar dades d'un altre.
- Cap adjunt és accessible directament des d'un disc públic.
- Les rutes superen `RouteNameContractTest`.
- La interfície seguix els patrons existents de Bootstrap 5 i Livewire 3.
