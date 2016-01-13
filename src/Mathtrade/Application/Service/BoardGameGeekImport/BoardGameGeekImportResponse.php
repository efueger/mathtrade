<?php
namespace Edysanchez\Mathtrade\Application\Service\BoardgamegeekImport;

class BoardGameGeekImportResponse
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