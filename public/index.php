<?php

namespace Hexlet\Project;

require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use DI\Container;


$container = new Container();

$container->set('renderer', function() {
    return new \Slim\Views\PhpRenderer('../templates');
});

$container->set('flash', function() {
    return new \Slim\Flash\Messages();
});

$container->set(\PDO::class, function() {
    $conn = new \PDO('pgsql:dbname=hexlet host=localhost', $user, $pass);
    $conn->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
    return $conn;
});

$app = AppFactory::createFromContainer($container);

$app->addErrorMiddleware(true, true, true);
$app->add(MethodOverrideMiddleware::class);
$router = $app->getRouteCollector()->getRouteParcer();


$app->get('/', function ($request, $response) {
    $params = [];
    return $this->get('renderer')->render($response, 'index.phtml', $params);
});

$app->post('/urls/new', function ($request, $response) {
    $url = $request->getParsedBodyParam('url');
    $siteRepositry = new SiteRepositry($this->get(\PDO::class));
    $validator = new Validator();
    $errors = $validator->validate($url);
    if (empty($errors)) {
        $site = Site::fromArray([$url['name'], Carbon::now()]);
        $siteRepositry = save($site);
    };
    $params = [];
    return $this->get('renderer')->render($response, 'index.phtml', $params);
});

$app->run();
