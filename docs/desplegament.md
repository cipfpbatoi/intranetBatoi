# Desplegar la intranet en una nova màquina
Instal·lem el sistema operatiu, preferiblemente sense entorn gràfic. La versió de PHP ha de ser al menys la 7.2

## Instal·lar el software
Els paquets a instal·lar són:
* **apache2**
* **mysql-server** o **mariadb-server** (recorda que després hem d'executar el comando **`mysql_secure_installation`** que configura l'usuari root). NOTA: ara la validació dels usuaris la fa el sistema (el _plugin_ 'auth_socket' o 'unix_socket'). Per a configurar un usuari amb privilegis consulta [StackOverflow](https://stackoverflow.com/questions/39281594/error-1698-28000-access-denied-for-user-rootlocalhost) o qualsevol altra pàgina en internet. En resum, executem:
```bash
sudo mysql -u root

mysql> USE mysql;
mysql> SELECT User, Host, plugin, authentication_string FROM mysql.user;
### Si uso Mysql le cambio el plugin y le pongo una contraseña
mysql> ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'P@ssw0rd';
### Si uso MariaDB le cambio el password al usuario
mysql> UPDATE user SET password=PASSWORD('your_p@ssw0rd') WHERE User='root';
mysql> UPDATE user SET plugin='auth_socket' WHERE User='root';
### en ambos casos
mysql> FLUSH PRIVILEGES;
mysql> exit;

sudo systemctl restart mysql.service    # o mariadb.service
```
Fuente correcta para mysql:[How To Install MySQL on Ubuntu 18.04](https://www.digitalocean.com/community/tutorials/how-to-install-mysql-on-ubuntu-18-04#step-2-%E2%80%94-configuring-mysql)

> Altra alternativa és instal·lar una versió més recent desde els repositoris, consulta [MariaDB Downloads](https://downloads.mariadb.org/mariadb/repositories/#mirror=tedeco&distro=Ubuntu&distro_release=bionic--ubuntu_bionic&version=10.3), o més senzill encara, tras instal·lar _phpmyadmin_ donem permisos a l'usuari phpmyadmin i utilitzem eixe usuari:
```bash
sudo mysql -u root

mysql> USE mysql;
mysql> GRANT ALL PRIVILEGES ON *.* TO 'phpmyadmin'@'localhost';
mysql> FLUSH PRIVILEGES;
mysql> exit;

sudo systemctl restart mysql.service
```

* **php**
* **phpmyadmin** (abans hem d'haver configurat el mysql)
* **git**
* **composer**

### Configurar apache
Creem els certificats (el _.key_ en /etc/ssl/private i els altres 2 en en /etc/ssl/certs):
```bash
openssl genrsa -out intranet.key 1024
openssl req -new -key intranet.key -out intranet.csr   # completem la informació que ens demanen
openssl x509 -req -in intranet.csr -signkey intranet.key -out intranet.crt
```

Posem en /etc/hosts el nom de la màquina (p.ej. `intranet.my`).

Configurem el lloc web SSL en _/etc/apache2/sites-available_:
* ServerName: p.ej. `ServerName intranet.my`
* DocumentRoot: `DocumentRoot /var/www/html/intranetBatoi/public`
* SSLCertificateFile: `SSLCertificateFile /etc/ssl/certs/intranet.crt`
* SSLCertificateKeyFile: `SSLCertificateKeyFile /etc/ssl/certs/intranet.key`
* Creem un nou directori:`
```bash
<Directory /var/www/html/intranetBatoi/public>
  AllowOverride All
  Order Allow,Deny
  Allow from All
</Directory>
```

Configurem el lloc web no SSL en _/etc/apache2/sites-available_ per a que redireccione al SSL:
* ServerName: p.ej. `ServerName intranet.my`
* Redireccionem: `Redirect permanent  /  https://intranet.my/`
* DocumentRoot: `DocumentRoot /var/www/html/intranetBatoi/public`

Habilitem els sites si els hem creat nous:
```bash
sudo a2ensite intranet.conf
sudo a2ensite intranet-ssl.conf
```

Configurem el **php.ini** (en _/etc/php/7.x/apache2/_) per a poder subir els fitxers de Itaca que són molt grans. També és convenient indicar la hora local:
```bash
post_max_size=0
upload_max_filesize = 200M
date.timezone = Europe/Madrid
```

Posem el nostre domini en el **/etc/hosts**:
```bash
127.0.0.1   intranet.my
```

Per a finalitzar hem d'activar (si no ho estan ja) els mòduls **ssl** i **rewrite** i reiniciar apache:
```bash
sudo a2enmod ssl
sudo a2enmod rewrite
sudo systemctl restart apache2.service
```
ATENCIÓ: cal que estiga la carpeta intranetBatoi ja creada abans de reiniciar Apache per que no done un error.

## Instal·lar el servidor de correu
Instal·lem el servidor de correu **exim4** y després el configurem:
```bash
sudo apt install exim4
sudo dpkg-reconfigure exim4-config
```

El que ens pregunta és:
* General type of mail configuration: _mail sent by smarthost: received via SMTP of fetchmail_
* System mail name: _intranet.my_ (o el nom que haguem donat al domini en .env)
* IP-addresses to listen on for incoming SMTP connections: _127.0.0.1 ; ::1_
* Other destinations for which mail is accepted: buit
* Machines to relay mail for: buit
* IP address or host name of the outgoing smarthost: _smtp.gmail.com::587_
* Hide local mail name in outgoing mail? _No_
* Keep number of DNS-queries minimal (Dial-on-Demand)? _No_
* Delivery method for local mail: _mbox format in /var/mail/_
* Split configuration into small files? _No_
* Root and Postmaster mail recipient: buit

A continuació editem el fitxer **/etc/exim4/passwd.client** per posar el nom i contrasenay del compte de gmail a utilitzar per a enviar els correus:
```bash
### target.mail.server.example:login:password
*.google.com:intranet@gmail.com:abcd@1234
```

Per a finalitzar hem de configurar el compte de GMail per a permetre a exim eixir. Es fa des de **GMail -> Configuració -> Compte -> Configuració del compte de Google -> Inici de sessió i seguretat -> Aplicacions amb accés al compte -> Permet l'accés a les aplicacions menys segures** i ACTIVEM aquesta opció:

![Activar compte Google](./img/exim-google.png)

Podem conprovar que funciona correctament enviant un e-mail des de la terminal:
```bash
mail el_meu_micorreu@gmail.com
```
