<?php

namespace Hexlet\Project;

require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use DI\Container;
use Slim\Middleware\MethodOverrideMiddleware;
use Carbon\Carbon;
use Valitron\Validator;

$container = new Container();

$container->set('renderer', function () {
    return new \Slim\Views\PhpRenderer('../templates');
});

$container->set('flash', function () {
    return new \Slim\Flash\Messages();
});

$container->set(\PDO::class, function () {
    return Connector::get()->connect();
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

$app->post('/urls', function ($request, $response) use ($router) {
    $urlData = $request->getParsedBody();
    $url = $urlData['url'];
    $repo = $this->get(SiteRepositry::class);
    $v = new Validator($url);
    $v->rule('required', "name")->message('URL не должен быть пустым')
        ->rule('url', "name")->message('Некорректный URL')
        ->rule('lengthMax', "name", 255)->message('Некорректный URL')
        ->stopOnFirstFail();
    if ($v->validate()) {
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
        $redirect = $router->urlFor('url', ['id' => $id]);
        return $response->withHeader('Location', $redirect)->withStatus(303);
    };
    $params = ['data' => $url['name'], 'errors' => $v->errors()];
    return $this->get('renderer')->render($response->withStatus(422), 'index.phtml', $params);
});

$app->get('/urls/{id}', function ($request, $response, $args) {
    $repo = $this->get(SiteRepositry::class);
    $url = $repo->find($args['id']);
    $checker = $this->get(Checker::class);
    $checks = $checker->getChecks($args['id']);
    $flash = $this->get('flash')->getMessages();
    $params = ['url' => $url, 'flash' => $flash, 'checks' => $checks];
    return $this->get('renderer')->render($response, 'urls/show.phtml', $params);
})->setName('url');

$app->post('/urls/{id}/checks', function ($request, $response, $args) use ($router) {
    $id = $args['id'];
    $repo = $this->get(SiteRepositry::class);
    $url = $repo->find($args['id']);
    $checker = $this->get(Checker::class);
    $status = $checker->makeCheck($id, $url->getName()); //??????
    if (!$status) {
        return $this->get('renderer')->render($response, '404.phtml', []);
    }
    $this->get('flash')->addMessage(...$status);
    $redirect = $router->urlFor('url', ['id' => $id]);
    return $response->withHeader('Location', $redirect)->withStatus(303);
});

$app->run();
