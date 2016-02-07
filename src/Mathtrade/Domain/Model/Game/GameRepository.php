<?php

namespace Edysanchez\Mathtrade\Domain\Model\Game;

interface GameRepository
{

    /**
     * @param $userName
     * @param Game $game
     */
    public function add($userName, Game $game) ;

    /**
     * @param  int   $id
     * @param  Game  $game
     * @return mixed
     */
    public function isGameImportedByUser($id, Game $game);

}
