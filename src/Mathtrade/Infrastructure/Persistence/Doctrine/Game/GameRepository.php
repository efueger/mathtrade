<?php


namespace Edysanchez\Mathtrade\Infrastructure\Persistence\Doctrine\Game;


use Doctrine\DBAL\DriverManager;
use Edysanchez\Mathtrade\Domain\Model\Game\Game;
use Edysanchez\Mathtrade\Domain\Model\Game\GameRepository as BaseGameRepository;
use Edysanchez\Mathtrade\Infrastructure\Persistence\Doctrine\DoctrineClient;

class GameRepository implements BaseGameRepository
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
     * @param $userId
     * @param Game $game
     */
    public function add($userId, Game $game)
    {
        $this->connection->insert('newitems', array(
            'account_id' => $userId,
            'name' =>$game->name(),
            'description' =>$game->description(),
            'bgg_id' => $game->boardGameGeekId(),
            'bgg_img' => $game->thumbnail(),
            'collid' => $game->collectionId(),

        ));
    }

    public function isGameImportedByUser($userId, Game $game)
    {
        $results = $this->connection->fetchAll(
            'SELECT id FROM newitems WHERE account_id = ? AND collid = ?',
            array($userId, $game->collectionId())
        );
        return 0<count($results);
    }

    public function find($id)
    {
        $gamesSql = "SELECT id,account_id,name,description,bgg_id,bgg_img,collid FROM newitems where id =?";
        $gamesResultset = $this->connection->fetchAll($gamesSql,array($id));

        $game = $this->makeGame($gamesResultset[0]);
        return $game;


    }

    /**
     * @param $plainGame
     */
    private function makeGame($plainGame)
    {
        $game = new Game($plainGame['id'], $plainGame['name']);
        $game->setCollectionId($plainGame['collid']);
        $game->setThumbnail($plainGame['bgg_img']);
        $game->setDescription($plainGame['description']);
        $game->setBoardGameGeekId($plainGame['bgg_id']);
        $game->setUserId($plainGame['account_id']);
        return $game;
    }
}
