# Cómo instalar la intranet

## Configurar la máquina
Lo más sencillo escon vagrant.

## Crear la BBDD

## Descargar el código
Creamos la carpeta que vaya a contener nuestro código y vamos a ella:
```bash
mkdir ~/Code/IntranetBatoi
cd ~/Code/IntranetBatoi
```

Inicializamos git:
```bash
git init
git remote add origin https://github.com/cipfpbatoi/intranetBatoi56.git
```

Configuramos el acceso a la BBDD:
```bash
cp .env.example .env
```
Y editarmos el fichero .env modificando las variables:
```bash
APP_KEY: pondremos la clave pública generada en nuestra carpeta posteriormente con php artisan key:generate
APP_URL: ponemos la URL de nnuestra intranet (la misma que hayamos configurado en el Homestead.yaml), ej. http://intranet.app
DB_DATABASE: ponemos el nombre de nuestra BBDD
DB_USERNAME, DB_PASSWORD: el usuario y contraseña para acceder a la misma
```

Instalamos las librerías necesarias (esto tardará bastante pues debe bajarse muchas librerías de Internet):
```bash
composer update
npm install
```


Relizamos las configuraciones iniciales. 
