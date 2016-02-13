<?php

namespace Edysanchez\Mathtrade\Application\Service\GetImportableBoardGameGeekGames;

class GetImportableBoardGameGeekGamesResponse
{
    private $games;

    public function __construct($games)
    {
        $this->games = $games;
    }

    /**
     * @return mixed
     */
    public function games()
    {
        return $this->games;
    }
}
