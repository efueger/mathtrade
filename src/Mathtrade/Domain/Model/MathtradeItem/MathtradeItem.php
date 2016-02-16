<?php

namespace Edysanchez\Mathtrade\Domain\Model\MathtradeItem;

use Edysanchez\Mathtrade\Domain\Model\Game\Game;

class MathtradeItem
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var Game[]
     */
    private $games;

    public function __construct($id, $gameList)
    {
        $this->id = $id;
        $this->games = $gameList;
    }

    public function id()
    {
        return $this->id;
    }

    /**
     * @return \Edysanchez\Mathtrade\Domain\Model\Game\Game[]
     */
    public function games()
    {
        return $this->games;
    }

}
