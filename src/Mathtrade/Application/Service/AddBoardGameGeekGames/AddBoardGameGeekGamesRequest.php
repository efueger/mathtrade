<?php

namespace Edysanchez\Mathtrade\Application\Service\AddBoardGameGeekGames;

class AddBoardGameGeekGamesRequest
{
    private $userId;
    private $games;

    public function __construct($userId, $games)
    {

        $this->userId = $userId;
        $this->games = $games;
    }

    /**
     * @return mixed
     */
    public function games()
    {
        return $this->games;
    }

    /**
     * @return mixed
     */
    public function userId()
    {
        return $this->userId;
    }
}
