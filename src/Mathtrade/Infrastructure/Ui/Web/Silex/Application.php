<?php

namespace Edysanchez\Mathtrade\Infrastructure\Ui\Web\Silex;

use DerAlex\Silex\YamlConfigServiceProvider;
use Edysanchez\Mathtrade\Application\Service\GetAllItems\GetAllItemsUseCase;
use Edysanchez\Mathtrade\Infrastructure\Persistence\Doctrine\Item\ItemRepository;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Symfony\Component\HttpFoundation\Response;

class Application
{
    public static function bootstrap()
    {
        $app = new \Silex\Application();

        $app['debug'] = true;

        $app->register(new YamlConfigServiceProvider(__DIR__ . '/config/settings.yml'));

        $app->register(new TwigServiceProvider(), array(
            'twig.path' => __DIR__ . '/../../Twig/views',
        ));

        $app->register(new SessionServiceProvider());

        $app->register(new DoctrineServiceProvider(), array(
            'db.options' => $app['config']['database']
        ));

        $app['item_repository'] = $app->share(function () {
            $repo = new ItemRepository();
            return $repo;
        });

        $app['get_all_items'] = $app->share(function () use ($app) {
            return new GetAllItemsUseCase($app['item_repository']);
        });


        $app->get('/all_items', function () use ($app) {
            $getAll = $app['get_all_items']->execute();
            return new Response(json_encode($getAll));

        });

        return $app;
    }
}
