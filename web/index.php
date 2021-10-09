<?php

require('../vendor/autoload.php');

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

$app = new Silex\Application();
$app['debug'] = true;

// Register the monolog logging service
$app->register(new Silex\Provider\MonologServiceProvider(), array(
  'monolog.logfile' => 'php://stderr',
));

// Register view rendering
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));

class Response {
  public function __construct($status, $data) {
    $this->status = $status;
    $this->data = $data;
  }
}

$app->get('/', function() use($app) {
  $obj = (object) array('status' => 'OK');
  return $app->json($obj, 200);
});

$app->register(new Csanquer\Silex\PdoServiceProvider\Provider\PDOServiceProvider('pdo'),
  array(
  'pdo.server' => array(
      'driver'   => 'mysql',
      'user' => $_ENV['DB_USER'],
      'password' => $_ENV['DB_PASSWORD'],
      'host' => $_ENV['CLEARDB_DATABASE_URL'],
      'port' => (int) $_ENV['DB_PORT'],
      'dbname' => $_ENV['DB_NAME']
      )
  )
);

$app->get('/couriers', function() use($app) {
  $st = $app['pdo']->prepare('SELECT id, name FROM couriers');
  $st->execute();

  $names = [];
  while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
    $names[] = $row;
  }

  $obj = ['status' => 'success','data' => $names];
  return $app->json($obj, 200);
});

$app->get('/couriers/{id}', function($id) use($app) {
  $st = $app['pdo']->prepare("SELECT * FROM shipments WHERE id={$id}");
  $st->execute();

  $services = [];
  while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
    $services[] = $row;
  }

  $obj = ['status' => 'success','data' => $services];
  return $app->json($obj, 200)->setEncodingOptions(JSON_NUMERIC_CHECK);
});

$app->error(function (\Exception $e) use ($app) {
  if ($e instanceof NotFoundHttpException) {
    return $app->json(array('error' => 'Page Not Found'), 404);
  }

  $code = ($e instanceof HttpException) ? $e->getStatusCode() : 500;
  return $app->json(array('error' => $e->getMessage()), $code);
});

$app->run();
