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
    private $game;

    public function __construct($id, $game)
    {
        $this->id = $id;
        $this->game = $game;
    }

    public function id()
    {
        return $this->id;
    }

    /**
     * @return \Edysanchez\Mathtrade\Domain\Model\Game\Game[]
     */
    public function game()
    {
        return $this->game;
    }
}
