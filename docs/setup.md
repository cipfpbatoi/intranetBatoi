# Cóm instal·lar la intranet

## Configurar la màquina
### Vagrant Homestead
Si la nostra màquina és per a desenvolupar l'aplicació el més senzill és crear un entorn amb Vagrant i Homestead. En la [documentació de Laravel](https://laravel.com/docs/5.6/homestead) hi ha informació de còm crear i configurar eixe entorn.

A l'hora de descarregar el codi, creem la carpeta que vaja a contenir el nostre codi i anem a ella:
```bash
mkdir ~/Code/IntranetBatoi
cd ~/Code/IntranetBatoi
```

Inicialitzem git i descarregem l'aplicació:
```bash
git init
git remote add origin https://github.com/cipfpbatoi/intranetBatoi56.git
git pull origin master
```
Instal·lem les llibreries necessàries (això tardarà prou perquè ha de baixar-se moltes llibreries de Internet):
```bash
composer update
npm install
```
En ENVARS de la configuració d'Apache és convenient posar com usuari i grup de apache al nostre usuari en compte de **www-data** per a no tindre problemes amb els permissos dels fitxers.

### Màquina nova
Si la màquina és només per a allotjar la intranet i no per a desenvolupament és millor crear nosaltres la màquina virtual o fer la instal·lació sobre una màquina real. El procediment a seguir per a instal·lar l'entorn necessario està explicat en el document [Desplegar la intranet en una nova màquina](./desplegament.md).

Per a descarregar el codi simplement anem on vullgam allotjar la intranet (p.e. _/var/www/html_), la baixem de github i descarregem les llibreries necessàries del composer:
```bash
git clone https://github.com/cipbatoi/intranetBatoi.git
composer update
```
Ens hem d'assegurar que l'usuari _www-data_ tinga permís d'escriptura en la carpeta **/storage** (per exemple posa-li el grup propietari _www-data- i dona permissos d'escriptura al grup).

## Configurar la intranet
Configurem l'accès a la BBDD:
```bash
cp .env.example .env
```
I editem el fitxer _.env_ modificant les variables:
```bash
APP_KEY: debe contener la clave generada con php artisan key:generate (se hace automáticamente)
APP_URL: URL de nnuestra intranet (la indicada en Homestead.yaml), ej. http://intranet.my
DB_DATABASE: ponemos el nombre de nuestra BBDD
DB_USERNAME, DB_PASSWORD: el usuario y contraseña para acceder a la misma
```
i afegint:
```bash
SESSION_DOMAIN: URL de nnuestra intranet (como APP_URL pero sin http), ej. intranet.my
GOOGLE_ID: nuestro identificador para utilizar el login de Google para loguearnos en la intranet
GOOGLE_SECRET: nuestra clave
GOOGLE_REDIRECT: la url que lo gestiona, ej. 'http://intranet.my/social/callback/google'
```

Relitzem les configuracions inicials. 
```bash
php artisan key:generate      # Genera la clave de la aplicación y la añade a APP_KEY en el fichero .env
```

Per últim només queda recarregar el fitxer de configuració en la caché (cal fer-ho cada vegada que canviem alguna cosa en qualsevl fitxer de configuració):
```bash
php artisan config:cache
```
El propietari de toda la carpeta i el seu contingut hauria de ser l'usuari **www-data**.

## Crear la BBDD
Creem la BBDD i li donem permissos l'usuari configurat en el fitxer _.env_. A continuació per a que es creen les taules executem:
```bash
php artisan migrate     # crea las tablas en la base de datos
php artisan db:seed     # inserta los datos iniciales de algunas tablas
```
El `db:seed` fica les dades inicials de:
* menu:
* ciclos:
* horas:
* departamentos:
* tipoincidencias
Així com un usuari amb el codi 9999 i la contrasenya 'abcd@1234' que és Administrador de la intranet.

### Altres dades inicials necessarios
Les dades de les taules de _muninipios_ i _provincias_ no els obtenim de Itaca sinó que els hem de importar a ma si els volem (és opcional,s'utilitzen només en alguns llistats).

## Importar dades de Itaca
Abans de fer la primera importació de dades de Itaca hem d'obrir el fitxer **contacto.php** en _/config_ on configurem totes les dades del nostre centre. A més indicarem el NIF (en format Itaca, és a dir, amb un 0 davant) dels càrrecs així com:
* avisos->material: la persona que indiquen rebrà un misstage cada vegada que es canvia un material inventariable d'ubicacio
* incidències: ací posem a totes les persones que s'encarreguen de solventar incidències e el Centre. Són els usuaris a qui es pot asignar una incidència quan es crea.

També és convenient posar correctament les dades de les taules (això només cal fer-ho la primera vegada que es crea la intranet, la resta d'anys es conserven les dades):
* Departamentos
* Ciclos

La taula _Departamentos_ ha de tindre OBLIGATORIAMENT un departament (podem dir-li 'Desconegut' o com vulgam) amb **codi 99** que és al que s'asignaran els nous professors fins que es posen en el departament adequat.

### Primera importació
En el xml de Itaca per a la primera importació han d'estar les següents taules:
* Continguts (mòduls del centre)
* Ocupacions (codis de les ocupacions no lectives dels professors)
* Grups (grups del centre)
* Professors
* Alumnes
* Horaris grup (horaris lectius dels diferents grups)
* Horaris ocupacions (horaris no lectius dels profsesores)

Les importacions es fan des del menú **Administració -> Importació des de Itaca**. Al ser la primera cal marcar la casella '_Primera Importació anual_'.

![Importar dades itaca](./img/setupImportItaca1a-1.png)

A continuació seleccionen el fitxer amb les dades en format .XML i polsem 'Enviar'.

**ATENCIÖ: Aquest procés tardarà uns quants minuts en funció de la mida del fitxer XML. És molt important _NO TANCAR_ el navegador ni tornar a polsar '_Enviar_' fins que acabe**.

Tras importar les dades la primera vegada hurem d'assignar a ma **els professores als departaments** i **els grups als cicles** (posteriorment només haurem de tornar-ho a fer amb elo nous professors i els nous grups si hi haguera tras cada importació).

Els professors estan asignats per defecte al departament 99 ('Desconegut'). Cada professor pot posar el departament al que pertany editant el seu perfil. També des de direcció es pot posar cadascun al seu departament des de **Equip directiu -> Dades professors -> Editar perfil professor**.

Els grups por defecte no estan asignats a cap cicle. Per a fer-ho anem a **Professorat -> Gestió grups -> Editar grup** i li posem a cada grup el seu cicle.

Per a finalitzar hem de tornar a fer la importació per a que s'asignen correctament els mòduls als cicles al importar els horaris **sense la casella de 'Primera importació' MARCADA.

### Resta d'importacions
En el xml de Itaca per a la resta d'importacions només estaran les taules en que hi haja canvis, normalment:
* Alumnes per a reflectir les noves matrículas i baixes
* Profesores, si hay nuevos profesores
* Horaris grup i horaris ocupacions, si hi hagueren canvis

Marquem 'Assignar tutor' si hi ha nous tutors (si no, no cal) i deixem desmarcada la d'esborrar grups sense tutor.

Tras esperar uns minuts...


