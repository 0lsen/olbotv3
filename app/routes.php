<?php

use Slim\Http\Request;
use Slim\Http\Response;

$app->get('/healthcheck', function(Request $request, Response $response, array $args) {
    $response->getBody()->write('OK');
    return $response;
});

$app->post('/incoming', 'incoming:evaluate')
    ->add(new \OLBot\Middleware\KarmaMiddleware($container['storage'], $container['cache']))
    ->add(new \OLBot\Middleware\ParserMiddleware($container['storage'], $container['cache']))
    ->add(new \OLBot\Middleware\InstantResponseMiddleware($container['storage'], $container['cache']))
    ->add(new \OLBot\Middleware\AdressedMiddleware($container['storage'], $container['cache']))
    ->add(new \OLBot\Middleware\CommandMiddleware($container['storage'], $container['cache']))
    ->add(new \OLBot\Middleware\AllowedMiddleware($container['storage'], $container['cache']))
    ->add(new \OLBot\Middleware\MessageMiddleware($container['storage'], $container['message']));

// test route that mocks messages and logs and returns those instead (as json)
$app->post('/testing', 'incoming:evaluate')
    ->add(new \OLBot\Middleware\KarmaMiddleware($container['storage'], $container['cache']))
    ->add(new \OLBot\Middleware\ParserMiddleware($container['storage'], $container['cache']))
    ->add(new \OLBot\Middleware\InstantResponseMiddleware($container['storage'], $container['cache']))
    ->add(new \OLBot\Middleware\AdressedMiddleware($container['storage'], $container['cache']))
    ->add(new \OLBot\Middleware\CommandMiddleware($container['storage'], $container['cache']))
    ->add(new \OLBot\Middleware\AllowedMiddleware($container['storage'], $container['cache']))
    ->add(new \OLBot\Middleware\MessageMockMiddleware($container['storage'], $container['cache']))
    ->add(new \OLBot\Middleware\TestMiddleware($container['storage'], $container['cache']));