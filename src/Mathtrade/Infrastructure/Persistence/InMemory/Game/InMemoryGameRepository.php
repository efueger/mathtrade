<?php

namespace Mathtrade\Infrastructure\Persistence\InMemory\Game;

use Mathtrade\Domain\Model\Game;
use Mathtrade\Domain\Model\GameRepository;

class InMemoryGameRepository implements GameRepository
{
    protected $repo=[];
    protected $nextId=0;

    public function __construct()
    {

    }

    /**
     * @param $game
     * @return Game
     */
    public function persist($game)
    {
        $tmpGame = $this->find($game->id());
        if($tmpGame === null) {
            $game->setId($this->nextId);
            $this->nextId++;
            $this->repo[]=$game;

        } else {
            $tmpGame = $game;
        }
    }

    /**
     * @param $id
     * @return Game|null
     */
    public function find($id)
    {
        foreach($this->repo as $game)  {
            if($game->id() === $id) {
                return $game;
            }
        }
        return null;
    }
}
