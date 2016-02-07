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
}
