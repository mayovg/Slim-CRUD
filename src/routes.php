<?php
// Routes
//
/*
$app->get('/', function ($request, $response, $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml');
});*/


// Ruta inicial para twig
$app->get('/', function ($request, $response, $args) {
    return $this->view->render($response, 'index.twig');
})->setName('inicio');

// Ruta para cargar la vista que muestra todos usuarios
$app->get('/muestra', function ($request,$response, $args) {
    return $this->view->render($response, 'tabla.html');
})->setName('muestra.usuarios');

// Ruta para cargar la vista de agregar usuario
$app->get('/nuevo', function ($request, $response, $args) {
    return $this->view->render($response, 'formulario.html');
})->setName('usuario.nuevo');

// Ruta para agregar usuario
$app->post("/nuevo", "App\Controllers\ControladorUsuario:crea");

// Ruta para cargar el formulario de actualización de usuario 
$app->get('/actualiza/{id}', function ($request, $response, $args){
    return $this->view->render($response, 'actualiza.html');
})->setName('usuario.actualiza');

// Ruta para actualizar un usuario
$app->post('/actualiza/{id}', "ControladorUsuario:actualiza");
