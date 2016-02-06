<?php

namespace Edysanchez\Mathtrade\Infrastructure\Persistence\Doctrine\Item;

use Edysanchez\Mathtrade\Domain\Model;
use BadMethodCallException;
use Doctrine\DBAL\DriverManager;
use Edysanchez\Mathtrade\Domain\Model\Game;
use Edysanchez\Mathtrade\Domain\Model\Item\Item;

class ItemRepository implements Model\Item\ItemRepository
{
    private $connection;
    /**
     * DoctrineItemRepository constructor.
     */
    public function __construct()
    {
        $connectionParams = array(
            'dbname' => 'mathtrade',
            'user' => 'root',
            'password' => '',
            'host' => 'localhost',
            'driver' => 'pdo_mysql',
        );
        $this->connection = DriverManager::getConnection($connectionParams);
    }

    /**
     * @param Game|Item $game
     */
    public function add(Item $game)
    {
        throw new BadMethodCallException();
    }


    public function findAll()
    {
        $sql = "SELECT id,name,bgg_img as img,username as userName FROM items";
        $items = $this->connection->fetchAll($sql);
        $games = array();
        foreach ($items as $item) {
            $game = new Item(
                utf8_encode($item['id']),
                utf8_encode($item['name']),
                utf8_encode($item['img']),
                utf8_encode($item['userName'])
            );
            array_push($games, $game);
        }
        return $games;
    }

}
