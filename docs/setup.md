# Cómo instalar la intranet

## Configurar la máquina
Lo más sencillo es crear un entorno con Vagrant y Homestead. En la [documentación de Laravel](https://laravel.com/docs/5.6/homestead) hay información de cómo crear y configurar dicho entorno.

## Descargar el código
Creamos la carpeta que vaya a contener nuestro código y vamos a ella:
```bash
mkdir ~/Code/IntranetBatoi
cd ~/Code/IntranetBatoi
```

Inicializamos git y descargamos la aplicación:
```bash
git init
git remote add origin https://github.com/cipfpbatoi/intranetBatoi56.git
git pull origin master
```

Instalamos las librerías necesarias (esto tardará bastante pues debe bajarse muchas librerías de Internet):
```bash
composer update
npm install
```

Relizamos las configuraciones iniciales. 
```bash
php artisan key:generate      # Genera la clave de la aplicación y la añade a APP_KEY en el fichero .env

```

Configuramos el acceso a la BBDD:
```bash
cp .env.example .env
```
Y editarmos el fichero _.env_ modificando las variables:
```bash
APP_KEY: debe contener la clave generada con php artisan key:generate
APP_URL: URL de nnuestra intranet (la indicada en Homestead.yaml), ej. http://intranet.app
DB_DATABASE: ponemos el nombre de nuestra BBDD
DB_USERNAME, DB_PASSWORD: el usuario y contraseña para acceder a la misma
```
y añadiendo:
```bash
SESSION_DOMAIN: URL de nnuestra intranet (como APP_URL pero sin http), ej. intranet.app
GOOGLE_ID: nuestro identificador para utilizar el login de Google para loguearnos en la intranet
GOOGLE_SECRET: nuestra clave
GOOGLE_REDIRECT: la url que lo gestiona, ej. 'http://intranet.cipfpbatoi.es/social/callback/google'
```

Por último sólo queda recargar el fichero de configuración en la caché (hay que hacerlo cada vez que cambiemos algo en algún fichero de configuración):
```bash
php artisan config:cache
```

## Crear la BBDD
Creamos la BBDD y le damos permisos al usuario configurado en el fichero _.env_. A continuación para que se creen las tablas ejecutamos:
```bash
php artisan migrate
```

### Datos iniciales
Los datos de algunas tablas no los obtendremos de Itaca sino que los pondremos a mano.
* menú:
* ciclos:
* horas:
* departamentos:
* espacios:
* tipoincidencias
* municipios / provincias: se usan porque en la tabla de ALUMNOS sólo están los códigos

