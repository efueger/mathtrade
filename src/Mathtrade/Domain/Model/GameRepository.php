<?php

namespace Edysanchez\Mathtrade\Domain\Model;


interface GameRepository {
    /**
     * @param Game $game
     */
    public function add($game);

    /**
     * @param Game $game
     */
    public function save($game);

    /**
     * @param $id
     * @return Game
     */
    public function find($id);

    /**
     * @return Game
     */
    public function findAll();
}