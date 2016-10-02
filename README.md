<h3 align="center"><img src="hvsf.png"></h3>
<h3 align="center"><img src="favicon.png"></h3>

<h1 align="center">Slim PHP Micro Framework</h1>
<h2 align="center">Manual para hacer CRUD</h2>

** Contenido de este manual ** <br>
1. [Prerequisitos](#prerequisitos-para-usar-slim)<br>
2. [Instalación](#instalación)<br>
3. [Referencias](#referencias)<br>


### Prerequisitos para usar Slim

- PHP 5.5 o posterior
- Un servidor web con reescritura de URLs (el de PHP es suficiente).
- Sistema Manejador de Bases de Datos MariaDB <sup>[1](#foot1)</sup>.
- El Contenedor de Inyección de Dependencias Monolog.


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

### Instalación

Ahora tenemos que instalar Slim; la manera recomendada por sus desarrolladores es mediante PHP Composer. 
Para instalar Composer podemos descargar directamente el archivo composer.phar desde [getcomposer.org/download/](https://getcomposer.org/download/) y guardarlo en el directorio `src/` del proyecto  o si se prefiere, se puede usar el siguiente script<sup>[2](#foot2)</sup>:

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
y  guardarlo como `install-composer.sh` para ejecutarlo en terminal con el comando 

```
	$	sh install-composer.sh
```

Si deseas usar globalmente Composer debes mover el archivo composer.phar a la carpeta `/usr/local/bin/` con el comando 

```
	# 	mv composer.phar /usr/local/bin/composer
```
de este modo podrás ejecutar Composer escribiendo solo `composer` en consola en vez de `php composer.phar`.

Después de haber instalado Composer, basta con escribir el comando `php composer.phar require slim/slim` para instalar el framework.
*Si se descargó manualmente, se debe estar en el directorio donde se guardó el archivo `composer.phar` para poder ejecutarlo.*
Al ejecutar el comando anterior, se agregará Slim Framework como dependencia en el archivo `composer.json` (si no se tiene el archivo se creará). Además, se ejecutará el comando `composer install` para que las dependencias puedan estar disponibles en la aplicación.

Al revisar el directorio donde instalamos el framework podemos notar que se crearon los archivos `composer.json`, `composer.lock` y el directorio `vendor/`. Composer es el encargado de manejar estas dependencias y no nos conviene incluirlas en nuestro repositorio por lo que crearemos un archivo `.gitignore` en nuestro directorio (si aún no existe) y le agregaremos la siguiente linea:

```
	vendor/*
```
*Es tiempo de comenzar a escribir el código de nuestro proyecto.*

Una buena ventaja de Slim es que te proporciona un esqueleto básico para agilizar el desarrollo de tu aplicación, esto se hace mediante el comando `php composer.phar create-project slim/slim-skeleton [nombre-de-mi-app]` y composer creará todos los directorios y archivos necesarios para que empieces a trabajar directamente en tu app. Sin embargo, en este manual nos enfocamos en la creación de cada uno de los componentes para conocer mejor su funcionamiento.


### Referencias
> <a name="foot1">1</a>: Mariadbcom. (2016). Mariadbcom. Retrieved 25 September, 2016, from https://mariadb.com/blog/why-should-you-migrate-mysql-mariadb. <br>
> <a name="foot2">2</a>: How to install Composer programmatically?#. (n.d.). Retrieved September 25, 2016, from https://getcomposer.org/doc/faqs/how-to-install-composer-programmatically.md. <br>





