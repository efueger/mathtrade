<?php
namespace Edysanchez\Mathtrade\Application\Service\BoardGameGeekImport;

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
