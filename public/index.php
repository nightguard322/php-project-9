<?php

namespace Hexlet\Project;

require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use DI\Container;
use Slim\Middleware\MethodOverrideMiddleware;
use Carbon\Carbon;

CONST USER = 'sasha';
CONST PASS = '12345';

$databaseUrl = parse_url($_ENV['DATABASE_URL']);
$username = $databaseUrl['user'];
$password = $databaseUrl['pass'];
$hostname = $databaseUrl['host'];
$dbname = ltrim($databaseUrl['path'], '/');

$container = new Container();

$container->set('renderer', function() {
    return new \Slim\Views\PhpRenderer('../templates');
});

$container->set('flash', function() {
    return new \Slim\Flash\Messages();
});

$container->set(\PDO::class, function() {
    $conn = new \PDO("pgsql:dbname=$dbname host=$hostname", $username, $password);
    $conn->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
    return $conn;
});

$app = AppFactory::createFromContainer($container);

$app->addErrorMiddleware(true, true, true);
$app->add(MethodOverrideMiddleware::class);
$router = $app->getRouteCollector()->getRouteParser();


$app->get('/', function ($request, $response) {
    $params = ['data' => '', 'errors' => []];
    return $this->get('renderer')->render($response, 'index.phtml', $params);
});

$app->get('/urls', function ($request, $response) {
    $siteRepositry = new SiteRepositry($this->get(\PDO::class));
    $urls = $siteRepositry->getEntities();
    $params = ['urls' => $urls];
    return $this->get('renderer')->render($response, 'urls/index.phtml', $params);
});

$app->get('/urls/{id}', function ($request, $response, $args) {
    $siteRepositry = new SiteRepositry($this->get(\PDO::class));
    $url = $siteRepositry->find($args['id']);
    $params = ['url' => $url];
    return $this->get('renderer')->render($response, 'urls/show.phtml', $params);
});

$app->post('/urls', function ($request, $response) {
    $urlData = $request->getParsedBody();
    $url = $urlData['url'];
    $siteRepositry = new SiteRepositry($this->get(\PDO::class));
    $validator = new Validator();
    $errors = $validator->validate($url);
    if (empty($errors)) {
        $date = Carbon::now()->toDateTimeString();
        dd($date);
        $site = Site::fromArray([$url['name'], $date]);
        $siteRepositry->save($site);
        $id = $site->getId();
        return $response->withHeader('Location', "urls/{$id}")->withStatus(303);
    };
    $params = ['data' => $url['name'], 'errors' => $errors];
    return $this->get('renderer')->render($response, 'index.phtml', $params);
});

$app->run();
