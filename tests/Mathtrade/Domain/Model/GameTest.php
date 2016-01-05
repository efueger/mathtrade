<?php

namespace Edysanchez\Mathtrade\Domain\Model;

use Edysanchez\Mathtrade\Domain\Model\Game\Game;

class GameTest extends \PHPUnit_Framework_TestCase
{

    const A_GAME_NAME = 'name';

    const ANOTHER_GAME_NAME = 'name changed';

    const A_GAME_ID = 1;

    public function testGameShouldHaveNameAndDescription()
    {
        $game = new Game(self::A_GAME_ID, self::A_GAME_NAME );
        $this->assertEquals($game->name(), self::A_GAME_NAME);
    }
    public function testGamePropertiesSettersShouldChangePropertiesValues()
    {
        $game = new Game(self::A_GAME_ID, self::A_GAME_NAME);
        $game->setName(self::ANOTHER_GAME_NAME);
    }
}
