<?php 
// web/index.php
require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views',
));


// ... definitions
$app->get('/', function (Silex\Application $app) {
   return $app['twig']->render('index.twig', array(
        'name' => 'edgard',
    ));
});

$app->run();
?>
