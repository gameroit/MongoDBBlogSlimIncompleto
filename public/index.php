<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Factory\AppFactory;

$app = AppFactory::create();

require __DIR__ . '/connection.php';

$prefix = '/api';

$app->addBodyParsingMiddleware();

function createJsonResponse(ResponseInterface $response, array $data): ResponseInterface
{
    // Establecer el tipo de contenido de la respuesta
    $response = $response->withHeader('Content-Type', 'application/json; charset=utf-8');

    // Escribir la respuesta
    $response->getBody()->write(json_encode($data));

    // Devolver la respuesta
    return $response;
}

// Ruta para la interfaz web del blog
$app->get('/', function (RequestInterface $request, ResponseInterface $response) {
    $response->getBody()->write(file_get_contents(__DIR__ . '/blog.html'));
    return $response->withHeader('Content-Type', 'text/html');
});

// Definir las rutas de la aplicación
$app->get($prefix . "/", function (RequestInterface $request, ResponseInterface $response, array $args) use ($mongo) {
    echo file_get_contents('./api.html');

    return $response;
});

// Endpoint de prueba
$app->get($prefix . '/test', function (RequestInterface $request, ResponseInterface $response) {
    $data = [
        'status' => 200,
        'message' => 'API is working'
    ];

    return createJsonResponse($response, $data);
});

require __DIR__ . '/routes/posts.php';

// Interceptar todas las rutas no definidas
$app->any('{routes:.+}', function (RequestInterface $request, ResponseInterface $response) {
    $data = [
        'status' => 404,
        'message' => 'Route not found'
    ];
    return createJsonResponse($response->withStatus(404), $data);
});

$app->run();
