<h3 align="center"><img src="http://i.imgur.com/vQMml1a.png" style="width:60px"></h3>
<h1 align="center">Slim Micro Framework</h1>
<h2 align="center">Manual para hacer CRUD</h2>

### Contenido de este manual
1. [Prerrequisitos](#prerrequisitos-para-usar-slim)<br>
2. [Instalación](#instalación)<br>
3. [Creación de Base de datos](#base-de-datos)<br>
3.1. [Método en consola](#en-consola)<br>
3.2. [Método usando phpmyAdmin](#en-phpmyadmin)<br>
4. [Configuración de Slim](#configuración-de-slim)<br>
5. [Modelo](#modelo)<br>
6. [Controlador](#controlador)<br>
7. [Rutas](#rutas)<br>
8. [Vista](#vista)<br>
9. [Referencias](#referencias)<br>


### Prerrequisitos para usar Slim

- PHP 5.5 o posterior
- Un servidor web con reescritura de URLs 
- Sistema Manejador de Bases de Datos MySQL/MariaDB<sup>[1](#foot1)</sup>
- Eloquent (ORM) de Laravel
- Respect Validation
- Twig templates



### Instalación

La manera para instalar Slim recomendada por sus desarrolladores es mediante PHP Composer.

#### Instalación de Composer 

###### GNU/Linux (GNU plus Linux), MAC OS X y *BSD

Si usas una distribución como Arch Linux o basada en esta, composer está en los repositorios oficiales, así que puedes instalarlo con Pacman.

```
	# pacman -S composer
```

En caso de que no, para instalar Composer escribe en consola el siguiente comando:

```
	$ curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer
```


o si prefieres, puedes usar el siguiente script<sup>[2](#foot2)</sup>:

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
Guardado como `install-composer.sh` para ejecutarlo en terminal con el comando 

```
	$	sh install-composer.sh
```

**Nota**: Mediante este método, podemos mantener actualizado Composer, pero debes mover el archivo composer.phar a la carpeta `/usr/local/bin/` con el comando 

```
	# 	mv composer.phar /usr/local/bin/composer
```
de este modo podrás ejecutar Composer escribiendo solo `composer` en consola en vez de `php <Directorio>composer.phar`.

##### Microsoft Windows 

Si eres usuario de Windows debes descargar el archivo `Composer-Setup.*.exe` del repositorio oficial de Composer en Github, que está en [https://github.com/composer/windows-setup/releases/](https://github.com/composer/windows-setup/releases/tag/v4.5.0) y seguir las instrucciones que te de el instalador.

#### Instalación de Slim

Podemos crear un proyecto desde cero o usar el esqueleto que proporciona Slim, el cual nos da una configuración sencilla para empezar la aplicación, solo tienes que escribir en consola lo siguiente: 

```
	$ composer create-proyect slim/slim-skeleton crud-slim
```

Esto creará un nuevo directorio `crud-slim`con los archivos necesarios para comenzar a escribir la aplicación.

**Estructura del directorio** 

```
crud-slim
├── composer.json
├── composer.lock
├── CONTRIBUTING.md
├── dirstruct.txt
├── logs
│   ├── app.log
│   └── README.md
├── phpunit.xml
├── public
│   └── index.php
├── README.md
├── src
│   ├── dependencies.php
│   ├── middleware.php
│   ├── routes.php
│   └── settings.php
├── templates
│   └── index.phtml
├── tests
│   └── Functional
│       ├── BaseTestCase.php
│       └── HomepageTest.php
└── vendor/...

```

**Nota:** el directorio `vendor/` contiene muchos subdirectorios pero no es recomendado editar ninguno de los archivos que se contienen aquí ya que es donde están todas las dependencias que usaremos dentro de la aplicación y modificarlos afectaría el funcionamiento de esta.

Si ejecutamos `php -S localhost:8080 -t public public/index.php`en el directorio de nuestra aplicación y abrimos nuestro navegador en la dirección `localhost:8080` aparecerá la siguiente vista
<img alt="vista inicial del esqueleto de Slim" src="http://i.imgur.com/C2kGD1q.png">


### Base de datos

#### En Consola
Creamos una base de datos con el nombre `slim` 

```
$ mysql -u[nombre-de-usuario] -p
> CREATE DATABASE slim COLLATE = 'utf8_unicode_ci';
> \u slim
```

Agregamos la tabla `usuarios`.

```sql
>  CREATE TABLE usuarios (`id` BIGINT NOT NULL AUTO_INCREMENT,
	                     `nombre` VARCHAR (250) NOT NULL,
						 `correo` VARCHAR (250) NOT NULL,
						 `clave_acceso` VARCHAR (250) NOT NULL,
						 PRIMARY KEY (`id`));
```

#### En phpMyAdmin

Creamos la base de datos que usaremos para el crud:
	<img src="http://i.imgur.com/L3qJubY.png" alt="Creación de base de datos">

Creamos la tabla de usuarios:
	<img src="http://i.imgur.com/G9jvJES.png" alt="Creación de tabla usuarios">



### Configura Slim

Ahora que tenemos la base de datos, hay que agregarla a la configuración de Slim. Para esto, abrimos el archivo `settings.php` que  se encuentra en el directorio `src` y que contiene lo siguiente:

```php
<?php
return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header

        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__ . '/../templates/',
        ],

        // Monolog settings
        'logger' => [
            'name' => 'slim-app',
            'path' => __DIR__ . '/../logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],
    ],
];


``` 
agregamos después del campo  `logger` la configuración de nuestra base de datos

```php

	//Configuración de base de datos para Slim
	'db' => [
		'driver' => 'mysql',
		'host' => 'localhost',
		'database' => 'slim'	
		'username' => '<tu nombre de usuario en mysql>',
		'password' => '<tu contraseña>',
		'charset'   => 'utf8',
	    'collation' => 'utf8_unicode_ci',
        'prefix'    => '',
		],
		
```
Además, para cargar automáticamente las clases que crearemos más adelante, debemos agregar en el archivo `composer.json` un mapeo _autoload_ con la convención **PSR-4** de php.

```javascript
    "autoload": {
	"psr-4": {
	    "App\\": "src/"
		}
    }
```

### Modelo

Ahora hay que crear el **Modelo** para la aplicación. Aunque Slim no sigue el patrón de diseño MVC (Modelo-Vista-Controlador) de un modo convencional, nos conviene tener un directorio exclusivo para cada componente, así que crearemos un directorio para el modelo dentro de `src/`con nuestro explorador de archivos o con el comando `mkdir modelos` desde el directorio `src`.

Como sabemos, Slim no cuenta con una herramienta para el Mapeo Objeto-Relacional por defecto. Sin embargo, nos permite agregar una de otro framework escrito en PHP; en este caso usaremos **Eloquent**<sup>[3](#foot3)</sup> de Laravel.

Para agregar **Eloquent** a nuestro CRUD primero debemos pedirle a composer que lo agregue a las dependencias de nuestra aplicación.

```sh
	$ composer require illuminate/database "~5.1"
```

Luego agregamos **Eloquent** al **Contenedor de Inyección de Dependencias** (en adelante _CID_) de la aplicación. Abrimos el archivo `dependencies.php` que está en el directorio `src`y le agregamos 

```php
$container['db'] = function ($container) {
    $capsule = new \Illuminate\Database\Capsule\Manager;
    $capsule->addConnection($container['settings']['db']);
    return $capsule;
};
```
Para inicializar **Eloquent** en la aplicación hay que agregarlo también en el archivo `public/index.php` antes de `app->run();`
```php
$capsule = $app->getContainer()->get('capsule'); // toma el elemento capsule dentro del contenedor de la app
$capsule->bootEloquent(); // inicializa Eloquent
```
Creamos la clase **ModeloUsuario** dentro del directorio modelos.

 ```php
 <?php
namespace App\Modelos;

//importa Eloquent para usarlo en el modelo
use Illuminate\Database\Eloquent\Model as Eloquent;
n
class ModeloUsuario extends Eloquent
{
    // Define la llave primaria de la tabla usuarios
    protected $primaryKey = 'id';

    // Define el nombre de la tabla 
    protected $table = 'usuarios';
	
	  // Define los campos que pueden llenarse en la tabla
    protected $fillable = [
        'nombre',
        'correo',
        'clave_acceso',
    ];
}
```


### Controlador

También crearemos un directorio `controladores` dentro de `src`para los controladores de la aplicación. Una vez creado el directorio, crearemos dentro de él una clase de **ControladorUsuario** que manejará el funcionamiento de la aplicación. Además,como nuestros controladores harán validaciones, instalamos la herramienta para validaciones **Respect Validation**<sup>[4](#foot4)</sup> mediante **Composer**.

```
	$ composer require respect/validation
```


```php
<?php

namespace App\Controladores;

use App\Modelos\ModeloUsuario as Usuario; // para usar el modelo de usuario
use Slim\Views\Twig; // Las vistas de la aplicación
use Slim\Router; // Las rutas de la aplicación
use Respect\Validation\Validator as v; // para usar el validador de Respect


/**
 * Clase de controlador para el usuario de la aplicación
 */

class ControladorUsuario
{

    // objeto de la clase Twig
    protected $view;
	
	// objeto de la clase Router
	protected $router;
	

    /**
     * Constructor de la clase Controller
     * @param type Slim\Views\Twig $view - Vista
	 * @param type Slim\Router $router - Ruta
     */
    public function __construct(Twig $view, Router $router)
    {
		$this->view = $view;
		$this->router = $router;
    }

	
    /**
     * Verifica que los parametros que recibe el controlador sean correctos
     * @param type array $args - los argumentos a evaluar
     */
    public function validaArgs($args)
    {
        $valid = [
            // verifica que la id sea un entero
            v::intVal()->validate($args['id']),
			
            // verifica que se reciba una cadena de al menos longitud 2
            v::stringType()->length(2)->validate($args['nombre']),
            
			// verifica que se reciba un correo
            v::email()->validate($args['correo']),
            
			// verifica que no esté en blanco la contraseña
            v::notBlank()->validate($args['clave_acceso'])
        ];
                                                 
    }
	
	/**
	* Verifica la correctud de un conjunto de validaciones
	* @param type array $validaciones - el conjunto de validaciones a evaluar
	* @throws \Exception cuando las validaciones no están en un arreglo
	*/
	public static function verifica($validaciones)
	{
		if(!is_array($validaciones){
			throw new \Exception('Las validaciones deben estar en un arreglo');
		} else {
			foreach($validaciones as $v){
				if ($v == false) {
					return false; // todas las validaciones deben cumplirse para que sea correcto
				}
			}
			return true;
		}
	}

	/*-- Funciones del CRUD --*/
}
```

También hay que agregar el **ControladorUsuario** al _CID_ de la aplicación para que ésta pueda utilizarlo.

```php
$container['ControladorUsuario'] = function($container){
	return new App\Controladores\ControladorUsuario($container);
};
```

#### Crear
Recordemos que en **ModeloUsuario** definimos una parte _fillable_ para la tabla, esto se debe a que al ser `id` definido en la base de datos como un atributo auto incrementable, entonces solo necesitamos ingresar los otros 3 campos a la base de datos y lo haremos con esta función:

```php
    /**
     * Función para crear un usuario
     * @param type Slim\Http\Request $request - solicitud http
     * @param type Slim\Http\Response $response - respuesta http
     */
    public function crea($request, $response, $args)
    {
		/*
		 getParsedBody() toma los parametros del cuerpo de $request que estén
		 como json o xml y lo parsea de un modo que PHP lo  entienda 
		*/
		$param = $request->getParsedBody(); 
        
		$validaciones = $this->validaArgs($param); // hace las validaciones
		if(verifica($validaciones)){
		
			// evalua si el correo ya existe en la base de datos
            $correo_existente = Usuario::where('correo', $atr['correo'])->get()->first();
        
			// si el correo ya existe manda un error 403
            if($correo_existente){
                echo->$this->error('YA_ESTÁ_REGISTRADO_EL_CORREO',
                                   $request->getUri()->getPath(),
                                   404);
                return $this->response->withStatus(403);
            } else {
            
				//crea un nuevo usuario a partir del modelo
                $usuario = new Usuario;

                // asigna cada elemento del arreglo $atr con su columna en la tabla usuarios
                $usuario->nombre = $atr['nombre'];
                $usuario->correo = $atr['correo'];
                $usuario->clave_acceso = $atr['clave_acceso'];

                $usuario->save(); //guarda el usuario

                // crea una ruta para el usuario con su id
                $path =  $request->getUri()->getPath() . '/' . $usuario->id;

                return $response->withStatus(201); // el usuario fue creado con éxito
            }
		}
	} 
```

#### Leer

Aquí se ejemplifican dos funciones, una para mostrar todos los usuarios registrados y otra donde muestre un usuario en específico. La estructura de los templates `lista.twig` y `usuario.twig` que se mencionan se explicará con mayor detalle en la sección de [Vistas](#vista).

```php
    /**
     * Obtiene todos los usuarios de la tabla usuarios y los manda a la vista
	 * @param type Slim\Http\Request $request - solicitud http
	 * @param type Slim\Http\Response $response - respuesta http
     */
    public function listaUsuarios($request, $response, $args)
    {
		/* 
		la vista manda un arreglo de usuarios con la respuesta http,
		para que lo renderice en en el template lista.twig
		*/
		return $this->view->render($response, 'lista.twig', ['usuarios' => Usuario::all()]);
    }

    /**
     * Busca un usuario por su id
     * @param type Slim\Http\Request $request - la solicitud http
     * @param type Slim\Http\Response $response - la respuesta http
     * @param type array $args - argumentos para la función
     */
    public function buscaUsuarioID($request, $response, $args)
    {
        $id = $args['id'];

        $valid = [v::intVal()->validate($id)]; // verifica que la id sea un entero
n
        // si la validación es correcta
        if ($valid == true){
            
			$usuario = Usuario::find($id); // busca la id en la tabla usuarios
            if ($usuario){
			
                /*
				 si el usuario es encontrado, manda una respuesta con éste
				 y lo renderiza en el template usuario.twig 
				*/
				
                return $this->view->render($response, 'usuario.twig', $usuario);
            } else {
			
			/*
			Si no hay un usuario con la id de los parametros, entonces obtiene la uri de la solicitud,
			redirecciona a la lista de usuarios y regresa una respuesta con la uri y un status 404 (not found)
			*/
			
			$status = 404; 
			$uri = $request->getUri()->withQuery('')->withPath($this->router->pathFor('listaUsuarios'));
            return $response->withRedirect((string)$uri, $status);
        } else {
            // si la validación es falsa, regresa un error de bad request 
            return $response->withStatus(400);
        }
    } 
```
#### Actualizar
Ejemplo de una función para actualizar un usuario.
```php
	/**
	 * Actualiza un usuario
     * @param type Slim\Http\Request $request - la solicitud http
     * @param type Slim\Http\Response $response - la respuesta http
     * @param type array $args - argumentos para la función
	*/
	public function actualiza($request, $response, $args)
	{
		// busca un usuario la id del arreglo de parametros en la tabla usuarios
		$usuario = Usuario::find((int)$args['id']);
		
		if(!$usuario){
			/*
			Si no hay un usuario con la id de los parametros, entonces obtiene la uri de la solicitud,
			redirecciona a la lista de usuarios y regresa una respuesta con la uri y un estado 404 (not found)
			*/
            $status = 404; 
			$uri = $request->getUri()->withQuery('')->withPath($this->router->pathFor('listaUsuarios'));
            return $response->withRedirect((string)$uri, $status);
		} else{
			$data = $request->getParsedBody(); // guarda los argumentos de la solicitud en un arreglo
			$validaciones = $this->valida($data); // valida los datos
			if (verifica($validaciones)){
				$usuario->update($data); // Eloquent actualiza la información en la tabla 
				
				// regresa una respuesta con la uri y redirecciona a la vista especifica del usuario
				$uri = $request->getUri()->withQuery('')->withPath($this->router->pathFor('usuario', ['id' => $usuario->id]));
                return $response->withRedirect((string)$uri);
			}
		}
	}
```

#### Eliminar

Eloquent cuenta con tres maneras de eliminar elementos de una tabla: la primera es eliminar una instancia del modelo de la que no se conoce su llave primaria, esta usa la función `delete` pero su desventaja es que tiene que recuperar todo la instancia antes de llamar a `delete`; la segunda es, suponiendo que se conoce la llave primaria del modelo, llama la función `destroy` que elimina el modelo sin tener que recuperar la instancia completa; la tercera opción es mediante consultas, por ejemplo `$eliminados = Usuario::where('nombre','like','C%')->delete();` eliminaría a todos los usuarios cuyo nombre empiece con "C". Adicionalmente, Eloquent cuenta con _soft deleting_, es decir, el modelo no  se borra de la base de datos, sino que se le agrega un atributo `deleted_at` y que, según recomiendan los desarrolladores de Laravel, debería ser agregada una columna a la tabla para que contenga dichos atributos.
Para habilitar el método _soft deleting_ en la aplicación se debe agregar la clase `Illuminate\Database\Eloquent\SoftDeletes` en los modelos de la app.
El siguiente ejemplo usa `delete` para hacer validaciones y darle más robustez antes de eliminar los modelos, eres libre de usar cualquiera de las opciones disponibles.

```php
	/**
	 * Elimina un usuario
	 * @param type Slim\Http\Request $request - la solicitud http
     * @param type Slim\Http\Response $response - la respuesta http
     * @param type array $args - argumentos para la función
	 */
	 public function elimina($request, $response, $args)
	 {
		 $usuario = Usuario->find($args['id']);
		 $validaID = [v::intVal()->validate($id)];
		 if($usuario && $validaID){
			 // si existe el usuario y la validación es correcta, lo elimina
			 $usuario->delete();
		 }
		 /*
		 regresa una respuesta con la uri y redirecciona a la lista de usuarios,
		 se  haya o no eliminado el usuario
		 */
		 $uri = $request->getUri()->withQuery('')->withPath($this->router->pathFor('listaUsuarios'));
		 return $response->withRedirect((string)$uri);
    
	 }
	 
``` 
### Rutas

La implementación de rutas de Slim fue construida a partir de **FastRoute**<sup>[5](#foot5)</sup> y provee de métodos para poder trabajar con los métodos HTTP más comunmente usados, es decir _GET_, _POST_, _PUT_, _DELETE_, _PATCH_, _OPTIONS_ que pueden manejarse uno por uno o todos de manera generar con el método `any()`de Slim. Además, es posible manejar varios métodos en una sola ruta usando la función `map()`.
En nuestra aplicación, las rutas se encuentran en el archivo `src/routes.php` que contiene la ruta que carga la vista de la página inicial del esqueleto, no la necesitamos entonces puedes quitarla o comentarla para guiarte al crear las demás rutas. Usaremos los métodos _GET_ para cargar vistas y ver usuarios, _POST_ para crear usuarios, _PATCH_ para actualizar un usuario y _DELETE_ para eliminar. 

#### GET
Las rutas que solo manejar solicitudes HTTP _GET_ usan el método `get()` de Slim, que recibe como argumentos un patrón de ruta (con marcadores de posición opcionales) y una función callback que puede provenir de un controlador o declararse dentro de la misma ruta.

```php
	$app->get('/', function($request, $response, $args){
		return $this->view->render($response, "index.twig");
	})->setName('inicio');
```
Lo que hace esta ruta es, para el patrón "/" (que sería el patrón inicial del servidor) llamar a una función que regrese como respuesta la vista definida en el template `index.twig` y a esta ruta le asigna el nombre "inicio" para que las vistas puedan interpretarlas más fácilmente.

```php
	// ruta para cargar la vista de todos los usuarios registrados
	$app->get('/listaUsuarios', function ($request, $response, $args){
		return $this->view->render($response, 'listaUsuarios.twig');
	})->setName('listaUsuarios');
	
	/*
	ruta para cargar la vista de un usuario en especifico definido por su id
	empleando la función buscaUsuarioID() de la clase ControladorUsuario, 
	previamente agregada al CID de la aplicación
	*/
	$app->get('/listaUsuarios/{id}','ControladorUsuario:buscaUsuarioID')->setName('usuario.ver');
	
	// ruta para cargar el formulario para crear usuario
	$app->get('/nuevo', function($request, $response, $args){
		return $this->view->render($response, 'formulario_crea.twig');
	})->setName('usuario.crear');

	// ruta para cargar el formulario para actualizar usuario
	$app->get('/listaUsuarios/{id}/actualiza', function($request, $response, $args){
		return $this->view->render($response, 'formulario_actualiza.twig');
	})->setName('usuario.editar');
	
```

#### POST

Al igual que con las solicitudes _GET_, Slim cuenta con una función llamada `post()` para manejar las solicitudes _POST_. Esta función también recibe como parametros el patrón de la ruta (con marcadores de posición opcionales) y una función callback.

```php
	// ruta para crear un nuevo usuario
	$app->post("/nuevo", "ControladorUsuario:crea");
```
Se puede notar que esta ruta no uso la función `setName()` pues al haber ya una ruta con el mismo patrón ("/nuevo") pero usando distintos métodos, ambas pueden compartir el mismo nombre.

#### PATCH

Para _PATCH_ también se cumple lo mencionado antes para _GET_ y _POST_. Entonces, para actualizar, tendríamos algo de este estilo:
```php
	// ruta para actualizar un usuario
	$app->patch('listaUsuarios/{id}', 'ControladorUsuario:actualiza')->setName('usuario.actualizar');
```
#### DELETE

```php
	// ruta para eliminar un usuario
	$app->delete('listaUsuario/{id}', 'ControladorUsuario:elimina')->setName('usuario.eliminar');
```

### Vista
Ya sabemos que Slim no cuenta nativamente con una herramienta para generar las plantillas de sus vistas y, de hecho, las vistas solo son parte del cuerpo de las respuestas HTTP de PSR-7 que implementa Slim por lo que dependen necesariamente de las rutas. Sin embargo, pueden proveerse mediante **Composer** de componentes para dicho fin y ellos mismos proporcionan las implementaciones de dos de estos componentes, **Twig** y  **PHP-View**. Personalmente prefiero Twig ya que me causó menos problemas y en general tiene una estructura más clara pues su síntaxis está basada en jinja y django templates. Por supuesto que, como todo en Slim, el uso de los componentes es cuestión de gustos y pueden manejarse otras herramientas para generar nuestras vistas. 

Primero, y al igual que con el resto de dependencias, agregaremos **Twig** usando **Composer**.
```
	$ composer require slim/twig-view
```

**Nota**: si quieres usar **PHP-View** en vez de **Twig** solo sustituye `slim/twig-view` por `slim/php-view`

Después de instalar **Twig** hay que agregarlo también al _CID_ de la aplicación para que Slim lo registre como uno de los servicios y puede utilizarlo.

```php

$container['view'] = function ($c) {
    $settings = $c->get('settings')['renderer']; //nos indica el directorio donde están las plantillas
    $view = new Slim\Views\Twig($settings['template_path'], [
        'cache' => false,]); // puede ser false o el directorio donde se guardará la cache
		
    // instancia y añade la extensión especifica de slim
    $basePath =  rtrim(str_ireplace('index.php', '', $c['request']->getUri()->getBasePath()), '/');
    $view->addExtension(new Slim\Views\TwigExtension($c['router'], $basePath));

    return $view;
};

```
En una estructura básica de **Twig** nos encontraremos con 3 tipos de delimitadores:
- {% ... %}, usado para ejecutar sentencias como estructuras de control, o crear bloques.
- {{ ... }}, usado para mostrar el contenido de variables o el resultado de la evaluación de una expresion.
- {# ... #}, usado para comentarios en las plantillas 

Como el diseño de **Twig** se basa en plantillas, podemos crear una plantilla base `layout.twig` y heredarla al resto de plantillas.

```php

<!DOCTYPE html>
<html>
<head>
    <title>CRUD SLIM</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	
	{% block stylesheets %}
	
	{# Aquí incluimos los archivos CSS o CDN de CSS que usemos #}
	<link href="url de cdn" type="text/css" rel="stylesheet" />
	
	{# la función base_url() le indica a twig que busque desde el directorio raíz de proyecto #}
	
	<link href="{{ base_url() }}/directorio/de/css" type="text/css" rel="stylesheet" />
	
	{% endblock %}
	
	{%block scripts }
	
	{# Aqui incluimos los .js y otros scripts
	<script src="{{ base_url() }}/directorio/de/scripts"></script>
	
	{% endblock %}
</head>
<body>
{% block content %}{% endblock %}
</div>
</body>
</html>

```
Podemos tener las plantillas que hereden de `layout.twig` en otro directorio, por ejemplo `templates/crud` para mantener organizada la jerarquía entre estas.

Si recordamos las [rutas](#rutas), la vista que carga al iniciar la aplicación es `index.twig` que tendría una estructura como la siguiente:

```php
	
{% extends 'layout.twig' %}

{% block content %}

{# la función path_for('') llama la ruta con el nombre que recibe como parametro #} 
<a href="{{ path_for('listaUsuarios') }}">Lista los usuarios registrados</a>
<a href="{{ path_for('usuario.crear') }}">Agrega un nuevo usuario</a>

{% endblock %}
	
```

Estructura de `formulario_crea.twig`


```php

{% extends 'layout.twig' %}

{% block content %}

{# manda los datos del formulario a la ruta 'usuario.crear' con un método post #}
<form action="{{ path_for('usuario.crear') }}"  method="post" autocomplete="off">
	<label for="nombre">Nombre
		<input type="text" name="nombre" id="nombre" placeholder="Escribe tu nombre">
    </label>
    <label for="correo">Correo
		<input type="email" name="correo" id="correo" placeholder="Escribe tu correo">
    </label>
    <label for="clave_acceso">Contraseña
		<input type="password" name="clave_acceso" id="clave_acceso" placeholder="Escribe tu contraseña">
    <button type="submit">Agregar</button>


{% endblock %}

```

Estructura de `formulario_actualiza.twig`

```php

{% extends 'layout.twig' %}

{% block content %}

{# manda los datos del formulario a la ruta 'usuario.actualizar' con un método patch #}
<form action="{{ path_for('usuario.actualizar') }}"  method="patch" autocomplete="off">
	<label for="nombre">Nombre
		<input type="text" name="nombre" id="nombre" placeholder="Escribe tu nombre">
    </label>
    <label for="correo">Correo
		<input type="email" name="correo" id="correo" placeholder="Escribe tu correo">
    </label>
    <label for="clave_acceso">Contraseña
		<input type="password" name="clave_acceso" id="clave_acceso" placeholder="Escribe tu contraseña">
    <button type="submit">Actualiza</button>


{% endblock %}

```
Estructura de `listaUsuario.twig`

```php

{% extends 'layout.twig' %}

{% block content %}
<table>
	<thead>
		<tr>
			<th>Nombre</th>
			<th>Correo</th>
		</tr>
	</thead>
	<tbody>
		{# itera la tabla usuarios del modelo #}
		{% for usuario in usuarios %}
		<tr>
			<td>{{ usuario.nombre }}</td>
			<td>{{ usuario.correo }}</td>
			<td><a href="{{ path_for('usuario.ver') }}">ver</a></td>
			<td><a href="{{ path_for('usuario.eliminar') }}">eliminar</a><td>
		</tr>
		{% endfor %}
	</tbody>
</table>
{% endblock %}

```

Estructura de `usuario.twig`

```php
{% extends 'layout.twig' %}

{% block content %}

<h1>{{ usuario.nombre }}</h1>
<h2>{{ usuario.correo }}</h2>
<img src="http://i.imgur.com/Y9IVuHg.jpg"/>

{% endblock %}

```

Recuerda que la premisa de este framework es usar solo lo que consideres necesario y no más que eso; la escala de tu proyecto es definida por ti mismo y no por el framework.

### Referencias

> <a name="foot1">1</a>: Mariadbcom. (2016). Mariadbcom. Retrieved 25 September, 2016, from https://mariadb.com/blog/why-should-you-migrate-mysql-mariadb. <br>
> <a name="foot2">2</a>: How to install Composer programmatically?#. (n.d.). Retrieved September 25, 2016, from https://getcomposer.org/doc/faqs/how-to-install-composer-programmatically.md. <br>
> <a name="foot3">3</a>:Eloquent: Getting Started - Laravel - The PHP Framework For Web Artisans. (n.d.). Retrieved September 29, 2016, from https://laravel.com/docs/5.1/eloquent <br>
> <a name="foot4">4</a>: Effective Validation with Respect. Retrieved September 30, 2016, from https://websec.io/2013/04/01/Effective-Validation-with-Respect.html <br>
> <a name="foot5">5</a>: N. (2016). FastRoute. Retrieved 12 October, 2016, from https://github.com/nikic/FastRoute<br>
> Codecourse (2016, April 13). Authentication with Slim 3 Retrieved from https://www.youtube.com/playlist?list=PLfdtiltiRHWGc_yY90XRdq6mRww042aEC <br>
> Rob Allen’s DevNotes. (2016, July 28). Retrieved November 08, 2016, from https://akrabat.com/category/slim-framework/ <br>


