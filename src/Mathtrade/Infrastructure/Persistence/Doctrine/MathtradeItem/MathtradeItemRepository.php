<?php

namespace Edysanchez\Mathtrade\Infrastructure\Persistence\Doctrine\MathtradeItem;

use Edysanchez\Mathtrade\Domain\Model;
use BadMethodCallException;
use Doctrine\DBAL\DriverManager;
use Edysanchez\Mathtrade\Domain\Model\Game\Game;
use Edysanchez\Mathtrade\Domain\Model\MathtradeItem\MathtradeItem;
use Edysanchez\Mathtrade\Domain\Model\MathtradeItem\MathtradeItemRepository as BaseMathtradeItemRepository;
use Edysanchez\Mathtrade\Infrastructure\Persistence\Doctrine\DoctrineClient;

class MathtradeItemRepository implements BaseMathtradeItemRepository
{
    private $connection;

    /**
     * DoctrineItemRepository constructor.
     * @param DoctrineClient $client
     * @throws \Doctrine\DBAL\DBALException
     */
    public function __construct(DoctrineClient $client)
    {
        $connectionParams = array(
            'dbname' => $client->dbName(),
            'user' => $client->userName(),
            'password' => $client->password(),
            'host' => $client->host(),
            'driver' => $client->driver(),
        );
        $this->connection = DriverManager::getConnection($connectionParams);
    }

    /**
     * @param MathtradeItem $game
     */
    public function add(MathtradeItem $mathtradeItem)
    {
        throw new BadMethodCallException();
    }


    public function findAll()
    {
        $mathtradeItemSql = "SELECT id,item_id,created FROM items_mt";
        $mathtradeItemsResultSet = $this->connection->fetchAll($mathtradeItemSql);
        $mathtradeItems = array();
        $games = array();
        foreach ($mathtradeItemsResultSet as $mathtradeItemResult) {
            $gamesSql = "SELECT id,account_id,name,description,bgg_id,bgg_img,collid FROM newitems where id =?";
            $gamesResultsets = $this->connection->fetchAll($gamesSql,array($mathtradeItemResult['item_id']));
            $games = array();
            foreach ($gamesResultsets as $gamesResultset) {
                /**
                 * @var Game $game
                 */
                $game = new Game($gamesResultset['id'], $gamesResultset['name']);
                $game->setCollectionId($gamesResultset['collid']);
                $game->setThumbnail($gamesResultset['bgg_img']);
                $game->setDescription($gamesResultset['description']);
                $game->setBoardGameGeekId($gamesResultset['bgg_id']);
                $game->setUserId($gamesResultset['account_id']);

                $games[] = $game;
            }
            $mathtradeItem = new MathtradeItem(
                utf8_encode($mathtradeItemResult['id']),
                $games
            );
            array_push($mathtradeItems, $mathtradeItem);
        }
        return $mathtradeItems;
    }
}
