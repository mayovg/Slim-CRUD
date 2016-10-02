<?php

/**
 * CRUD del microframework Slim usado como ejemplo para la exposición.
 * <b>Tecnologías para Desarrollos en Internet.</b>
 * 2017-1
 * @author Luis Pablo Mayo
 * @see https://github.com/pmy0v/hivecrud
 */



/* Llama a las clases Request y Response para no tener que hacer referencias 
a ellas con sus nombres largos. Ambas son usadas por el estándar PSR-7 de PHP. */
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

/* Tenemos que usar el archivo de carga automática que instaló Composer. */
require '../vendor/autoload.php';

/* Para agregar más clases. */
pl_autoload_register(function ($classname) {
	require ("../classes/" . $classname . ".php");
});

/* Manejando dependencias con Monolog y una conexión de PDO a mysql/mariadb. */
$container = $app->getContainer();

/* Conexión con la base de datos */
$container['db'] = function ($c) {
    $db = $c['settings']['db'];
    $pdo = new PDO("mysql:host=" . $db['host'] . ";dbname=" . $db['dbname'],
        $db['user'], $db['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};

/* Crea un objeto de Slim app para iniciar a desarrollar con Slim. 
   Además, especifica donde se guardarán las configuraciones de la app para acceder a ellas después 
   si es necesario.*/
$app = new \Slim\App (["settings" => $config]);

/* la función get() nos permite hacer una solicitud GET a /hola/{nombre}*/
$app->get('/hola/{nombre}', function (Request $request, Response $response) {
	/* request recibe el atributo del nombre y lo guarda en $nombre. */
    $nombre = $request->getAttribute('nombre');
	/* response le envia al servidor una respuesta con el nombre que ha recibido. */
    $response->getBody()->write("Hola, $nombre");
    return $response;
});
$app->run(); //Para correr la aplicación en el servidor.

