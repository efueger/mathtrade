<?php
namespace Edysanchez\Mathtrade\Application\Service\GetImportableBoardGameGeekGames;

class GetImportableBoardGameGeekGamesRequest
{
    private $username;

    public function __construct($username)
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function username()
    {
        return $this->username;
    }
}
