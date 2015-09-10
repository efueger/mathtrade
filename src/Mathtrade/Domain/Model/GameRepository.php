<?php

namespace Mathtrade\Domain\Model;


interface GameRepository {
    /**
     * @param Game $game
     * @return mixed
     */
    public function persist($game);

    /**
     * @param $id
     * @return mixed
     */
    public function find($id);
}