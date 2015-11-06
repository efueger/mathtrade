<?php

namespace Edysanchez\Mathtrade\Infrastructure\Persistence\InMemory\Game;

use Edysanchez\Mathtrade\Domain\Model\Game;
use Edysanchez\Mathtrade\Domain\Model\GameRepository;
use Symfony\Component\DependencyInjection\Exception\BadMethodCallException;

class InMemoryGameRepository implements GameRepository
{
    protected $repo;
    protected $nextId=0;

    public function __construct()
    {
        $this->repo = array();
    }

    /**
     * @param $game
     * @return Game
     */
    public function persist($game)
    {
        $tmpGame = $this->find($game->id());
        if ($tmpGame === null) {
            $game->setId($this->nextId);
            $this->nextId++;
            array_push($this->repo, $game);
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
        foreach ($this->repo as $game) {
            if ($game->id() === $id) {
                return $game;
            }
        }
        return null;
    }

    /**
     * @param Game $game
     */
    public function add($game)
    {
        throw new BadMethodCallException();
    }

    /**
     * @param Game $game
     */
    public function save($game)
    {
        throw new BadMethodCallException();
    }


    /**
     * @return Game
     */
    public function findAll()
    {
        throw new \BadMethodCallException();
    }
}
