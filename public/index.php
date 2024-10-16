<?php

namespace Hexlet\Project;

require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use DI\Container;
use Slim\Middleware\MethodOverrideMiddleware;
use Carbon\Carbon;

$databaseUrl = parse_url($_ENV['DATABASE_URL'] ?? getenv('DATABASE_URL'));
$dbcreds = [
    'username' => $databaseUrl['user'],
    'password' => $databaseUrl['pass'],
    'hostname' => $databaseUrl['host'],
    'dbname' => ltrim($databaseUrl['path'], '/')
];

$container = new Container();

$container->set('renderer', function () {
    return new \Slim\Views\PhpRenderer('../templates');
});

$container->set('flash', function () {
    return new \Slim\Flash\Messages();
});

$container->set(\PDO::class, function () use ($dbcreds) {
    $conn = new \PDO(
        "pgsql:dbname={$dbcreds['dbname']} host={$dbcreds['hostname']}",
        $dbcreds['username'],
        $dbcreds['password']
    );
    $conn->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
    return $conn;
});

$app = AppFactory::createFromContainer($container);

$app->addErrorMiddleware(true, true, true);
$app->add(MethodOverrideMiddleware::class);
$router = $app->getRouteCollector()->getRouteParser();

session_start();
$app->get('/', function ($request, $response) {
    $params = ['data' => '', 'errors' => []];
    return $this->get('renderer')->render($response, 'index.phtml', $params);
});

$app->get('/urls', function ($request, $response) {
    $repo = new SiteRepositry($this->get(\PDO::class));
    $urls = $repo->getEntities();
    $params = ['urls' => $urls];
    return $this->get('renderer')->render($response, 'urls/index.phtml', $params);
});

$app->post('/urls', function ($request, $response) {
    $urlData = $request->getParsedBody();
    $url = $urlData['url'];
    $repo = $this->get(SiteRepositry::class);
    $validator = new Validator();
    $errors = $validator->validate($url);
    if (empty($errors)) {
        $id = $repo->findByName($url['name']);
        if ($id) {
            $this->get('flash')->addMessage('success', 'Страница уже существует');
        } else {
            $date = Carbon::now()->toDateTimeString();
            $site = Site::fromArray([$url['name'], $date]);
            $repo->save($site);
            $id = $site->getId();
            $this->get('flash')->addMessage('success', 'Страница успешно добавлена');
        }
        return $response->withHeader('Location', "urls/{$id}")->withStatus(303);
    };
    $params = ['data' => $url['name'], 'errors' => $errors];
    return $this->get('renderer')->render($response, 'index.phtml', $params);
});

$app->get('/urls/{id}', function ($request, $response, $args) {
    $repo = $this->get(SiteRepositry::class);
    $url = $repo->find($args['id']);
    $checker = $this->get(Checker::class);
    $checks = $checker->getChecks($args['id']);
    $flash = $this->get('flash')->getMessages();
    $params = ['url' => $url, 'flash' => $flash, 'checks' => $checks];
    return $this->get('renderer')->render($response, 'urls/show.phtml', $params);
});

$app->post('/urls/{id}/checks', function ($request, $response, $args) {
    $id = $args['id'];
    $repo = $this->get(SiteRepositry::class);
    $url = $repo->find($args['id']);
    $checker = $this->get(Checker::class);
    $checker->makeCheck($id, $url->getName());
    return $response->withHeader('Location', "/urls/{$id}");
});

$app->run();
