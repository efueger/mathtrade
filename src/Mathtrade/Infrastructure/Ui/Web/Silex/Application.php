<?php

namespace Edysanchez\Mathtrade\Infrastructure\Ui\Web\Silex;

use DerAlex\Silex\YamlConfigServiceProvider;
use Doctrine\DBAL\Connection;
use Edysanchez\Mathtrade\Application\Service\AddBoardGameGeekGames\AddBoardGameGeekGamesUseCase;
use Edysanchez\Mathtrade\Application\Service\ExportMathtradeData\ExportMathtradeDataUseCase;
use Edysanchez\Mathtrade\Application\Service\GetAllItems\GetAllItemsUseCase;
use Edysanchez\Mathtrade\Application\Service\GetAllMathtradeItems\GetAllMathtradeItemsUseCase;
use Edysanchez\Mathtrade\Application\Service\GetImportableBoardGameGeekGames\GetImportableBoardGameGeekGamesUseCase;
use Edysanchez\Mathtrade\Infrastructure\Persistence\Doctrine\DoctrineClient;
use Edysanchez\Mathtrade\Infrastructure\Persistence\Doctrine\Game\GameRepository;
use Edysanchez\Mathtrade\Infrastructure\Persistence\Doctrine\MathtradeItem\MathtradeItemRepository;
use Edysanchez\Mathtrade\Infrastructure\Persistence\Doctrine\WildCard\WildCardRepository;
use Edysanchez\Mathtrade\Infrastructure\Persistence\XmlApi\Game\BoardGameGeekRepository;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Symfony\Component\HttpFoundation\Response;

class Application
{

    public static function getUser($hash)
    {
        global $app;
        //Get the user
        $sql = "SELECT * FROM users WHERE hash = ?";
        $user = $app['db']->fetchAll($sql, array($hash));
        $user = $user[0];

        return $user;
    }

    public static function getWantUser($user)
    {
        global $app;
        $sql = "SELECT * FROM items where username = ?";
        $post = $app['db']->fetchAll($sql, array($user));

        //Fetch want lists
        $ids = array();
        foreach ($post as $i) {
            $ids[] = $i['id'];
        }
        $sql = "SELECT w.*,i.name".
            " FROM wantlist w INNER JOIN items i ON type =1 AND w.target_id = i.item_id".
            " WHERE w.item_id IN (?) ORDER BY pos ASC";

        $want = $app['db']->fetchAll(
            $sql,
            array($ids),
            array(Connection::PARAM_INT_ARRAY)
        );

        foreach ($post as $key => &$p) {
            foreach ($want as $j => $w) {
                if ($w['item_id'] == $p['item_id']) {
                    $w['id'] = $w['target_id'];
                    $p['wantlist'][] = $w;
                    unset($want[$j]);
                }
            }
        }

        return $post;
    }

    public static function logged()
    {
        global $app;
        if (null === $user = $app['session']->get('user')) {
            return $app->redirect('/signin');
        }

        return $user;
    }

    /**
     * @param $userName
     * @return string
     */
    public static function generateHash($userName)
    {
        return md5(time() . $userName . time());
    }

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

        $app['item_repository'] = $app->share(function () use ($app) {
            $repo = new MathtradeItemRepository($app['doctrine_client'], $app['game_repository']);

            return $repo;
        });

        $app['game_repository'] = $app->share(function () use ($app) {
            return new GameRepository($app['doctrine_client']);
        });

        $app['mathtrade_item_repository'] = $app->share(function () use ($app) {
            return new MathtradeItemRepository($app['doctrine_client'], $app['game_repository']);
        });

        $app['wildcard_repository'] = $app->share(function () use ($app) {
           return new WildCardRepository($app['doctrine_client'], $app['mathtrade_item_repository']);
        });

        $app['doctrine_client'] = $app->share(function () use ($app) {
            $databaseConfig = $app['config']['database'];

            return new DoctrineClient(
                $databaseConfig['dbname'],
                $databaseConfig['user'],
                $databaseConfig['password'],
                $databaseConfig['host'],
                $databaseConfig['driver']
            );
        });

        $app['add_board_game_geek_games'] = $app->share(function () use ($app) {
            return new AddBoardGameGeekGamesUseCase($app['game_repository']);
        });

        $app['get_all_mathtrade_items'] = $app->share(function () use ($app) {
            return new GetAllMathtradeItemsUseCase($app['item_repository']);
        });

        $app['board_game_geek_game_repository'] = $app->share(function () {
            return new BoardGameGeekRepository();
        });

        $app['board_game_geek_import'] = $app->share(function () use ($app) {
            return new GetImportableBoardGameGeekGamesUseCase($app['board_game_geek_game_repository']);
        });

        $app['export_mathtrade_data'] = $app->share(function () use ($app) {
           return new ExportMathtradeDataUseCase($app['mathtrade_item_repository'], $app['wildcard_repository']);
        });

        return $app;
    }
}
