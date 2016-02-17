<?php

namespace Edysanchez\Mathtrade\Infrastructure\Persistence\InMemory\Game;

use Edysanchez\Mathtrade\Domain\Model\Game\Game;
use Edysanchez\Mathtrade\Domain\Model\Game\GameRepository;

class InMemoryGameRepository implements GameRepository
{
    protected $repo;
    protected $nextId=0;

    public function __construct($repo)
    {
        $this->repo = $repo;
    }

    public function add($userName, Game $game)
    {
        $this->repo[$userName][] = $game;
    }

    public function findByUserName($userName)
    {
        return $this->repo[$userName];
    }

    /**
     * @param $userName
     * @param Game $game
     * @return bool
     */
    public function isGameImportedByUser($userName, Game $game)
    {
        $userGames = array();

        /** @var Game $ownedGame */
        foreach ($this->repo as $user => $games) {
            if ($userName === $user) {
                $userGames = $games;
            }
        }
        foreach ($userGames as $ownedGame) {
            if ($ownedGame->collectionId() === $game->collectionId()) {
                return true;
            }
        }

        return false;
    }

    public function find($id)
    {
        throw new \BadMethodCallException();
    }
}
