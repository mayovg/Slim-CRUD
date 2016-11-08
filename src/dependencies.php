<?php
// DIC configuration

$container = $app->getContainer();

// view renderer (deprecated)
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    return $logger;
};

// Eloquent ORM
$container['db'] = function ($container) {
    $capsule = new \Illuminate\Database\Capsule\Manager;
    $capsule->addConnection($container->get('settings')['db']);

    $capsule->setAsGlobal();
    $capsule->bootEloquent();

    return $capsule;
};

$container[App\WidgetController::class] = function($c) {
    $view = $c->get('renderer');
    $logger = $c->get('logger');
    $table = $c->get('db')->table('usuario');
    return new \App\WidgetController($view, $logger, $table);
};

// twig para las vistas y plantillas
$container['view'] = function ($c) {
    $settings = $c->get('settings')['renderer']; //nos indica el directorio donde están las plantillas
    $view = new Slim\Views\Twig($settings['template_path'], [
        'cache' => false,]);
    // instancia y añade la extensión especifica de slim
    $basePath =  rtrim(str_ireplace('index.php', '', $c['request']->getUri()->getBasePath()), '/');
    $view->addExtension(new Slim\Views\TwigExtension($c['router'], $basePath));

    return $view;
};

// controlador de usuario
$container['App\Controllers\ControladorUsuario'] = function($c) {
    return new App\Controllers\ControladorUsuario($c);
};
