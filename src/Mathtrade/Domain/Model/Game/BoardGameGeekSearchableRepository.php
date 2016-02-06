<?php


namespace Edysanchez\Mathtrade\Domain\Model\Game;


interface BoardGameGeekSearchableRepository
{
    /**
     * @param $username
     * @return Game []
     */
    public function findTradeableByUsername($username);

}