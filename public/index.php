<?php

// Подключение автозагрузки через composer
require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use DI\Container;

$container = new Container();
$container->set('renderer', function () {
    // Параметром передается базовая директория, в которой будут храниться шаблоны
    return new \Slim\Views\PhpRenderer(__DIR__ . '/../templates');
});
$app = AppFactory::createFromContainer($container);
$app->addErrorMiddleware(true, true, true);

//$app->addErrorMiddleware(true, true, true);

// return $response->getBody()->write('Welcome to Slim!');
// Благодаря пакету slim/http этот же код можно записать короче
// return $response->write('Welcome to Slim!');

// $app = AppFactory::create();

$users = ['mike', 'mishel', 'adel', 'keks', 'kamila'];

$app->get('/users', function ($request, $response) use ($users) {
  $name = $request->getQueryParam('name');
  $filteredNames = array_filter($users, fn($user) => str_contains($user, $name));
  $params = ['users' => $filteredNames, 'name' => $name];
  return $this->get('renderer')->render($response, 'users/index.phtml', $params);
});

$app->get('/users/{id}', function ($request, $response, $args) {
  $params = ['id' => $args['id'], 'nickname' => 'user-' . $args['id']];
  // Указанный путь считается относительно базовой директории для шаблонов, заданной на этапе конфигурации
  // $this доступен внутри анонимной функции благодаря https://php.net/manual/ru/closure.bindto.php
  // $this в Slim это контейнер зависимостей
  return $this->get('renderer')->render($response, 'users/show.phtml', $params);
});

$app->get('/courses/{id}', function ($request, $response, array $args) {
  $id = $args['id'];
  return $response->write("Course id: {$id}");
});

$app->get('/', function ($request, $response) {
    return $response->write('GET /');
});

$app->get('/companies', function ($request, $response) {
    return $response->write('GET /companies');
});

$app->post('/companies', function ($request, $response) {
    return $response->write('POST /companies');
});

$app->post('/users', function ($request, $response) {
  return $response->withStatus(302);
});

$app->run();