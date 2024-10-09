<?php

namespace Hexlet\Project;

require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use DI\Container;
use Slim\Middleware\MethodOverrideMiddleware;


$container = new Container();

$container->set('renderer', function() {
    return new \Slim\Views\PhpRenderer('../templates');
});

$container->set('flash', function() {
    return new \Slim\Flash\Messages();
});

// $container->set(\PDO::class, function() {
//     $conn = new \PDO('pgsql:dbname=hexlet host=localhost', $user, $pass);
//     $conn->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
//     return $conn;
// });
$pdo = new \PDO('sqlite:hexlet');
$app = AppFactory::createFromContainer($container);

$app->addErrorMiddleware(true, true, true);
$app->add(MethodOverrideMiddleware::class);
$router = $app->getRouteCollector()->getRouteParser();


$app->get('/', function ($request, $response) {
    $params = [];
    return $this->get('renderer')->render($response, 'index.phtml', $params);
});

$app->get('/urls', function ($request, $response) {
    $siteRepositry = new SiteRepositry($this->get(\PDO::class));
    $urls = $siteRepositry->getEntities();
    $params = ['urls' => $urls];
    return $this->get('renderer')->render($response, 'index.phtml', $params);
});

$app->get('/urls/{id}', function ($request, $response, $args) {
    $siteRepositry = new SiteRepositry($this->get(\PDO::class));
    $url = $siteRepositry->find($args['id']);
    $params = ['url' => $url];
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
