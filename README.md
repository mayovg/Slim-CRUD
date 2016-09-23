# Slim PHP Framework
## Manual para hacer CRUD

###Prerequisitos para usar Slim Framework

- PHP 5.5 o posterior
- Un servidor web con reescritura de URLs (Apache, Nginx, HipHop Virtual Machine, ISS, lighttpd o el servidor de PHP).



Para comenzar, creamos un directorio para 
el proyecto que tenga la siguiente estructura.

```
	├── proyecto
	│   └── src
	│       └── public

```

```sh
	mkdir proyecto/src/public
```

Ahora tenemos que instalar Slim Framework; la manera recomendada por sus desarrolladores es mediante PHP Composer. 
Para instalar Composer podemos descargar directamente el archivo composer.phar desde [getcomposer.org/download/](https://getcomposer.org/download/) y guardarlo en el directorio src/ del proyecto  o si se prefiere, se puede usar el siguiente script: 

```sh
#!/bin/sh

EXPECTED_SIGNATURE=$(wget https://composer.github.io/installer.sig -O - -q)
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
ACTUAL_SIGNATURE=$(php -r "echo hash_file('SHA384', 'composer-setup.php');")

if [ "$EXPECTED_SIGNATURE" = "$ACTUAL_SIGNATURE" ]
then
    php composer-setup.php --quiet
    RESULT=$?
    rm composer-setup.php
    exit $RESULT
else
    >&2 echo 'ERROR: Invalid installer signature'
    rm composer-setup.php
    exit 1
fi

```

Después de haber instalado Composer, basta con escribir el comando `php composer.phar require slim/slim` para instalar el framework.
**Si se descargó manualmente, se debe estar en el directorio donde se guardó el archivo `composer.phar` para poder ejecutarlo.**
Al ejecutar el comando anterior, se agregará Slim Framework como dependencia en el archivo `composer.json` (si no se tiene el archivo se creará). Además, se ejecutará el comando `composer install` para que las dependencias puedan estar disponibles en la aplicación.

Al revisar el directorio donde instalamos el framework podemos notar que se crearon los archivos `composer.json`, `composer.lock` y el directorio `vendor/`. Composer es el encargado de manejar estas dependencias y no nos conviene incluirlas en nuestro repositorio por lo que crearemos un archivo `.gitignore` en nuestro directorio (si aún no existe) y le agregaremos la siguiente linea:

```
	vendor/*
```








