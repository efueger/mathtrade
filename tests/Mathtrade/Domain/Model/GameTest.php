<?php

namespace Edysanchez\Mathtrade\Domain\Model;

class GameTest extends \PHPUnit_Framework_TestCase
{

    const A_GAME_NAME = 'name';
    const A_GAME_DESCRIPTION = 'description';

    const ANOTHER_GAME_NAME = 'name changed';
    const ANOTHER_GAME_DESCRIPTION = 'description changed';

    public function testGameShouldHaveNameAndDescription()
    {
        $game = new Game(self::A_GAME_NAME ,  self::A_GAME_DESCRIPTION );
        $this->assertEquals($game->name(), self::A_GAME_NAME);
        $this->assertEquals($game->description(), self::A_GAME_DESCRIPTION);
    }
    public function testGamePropertiesSettersShouldChangePropertiesValues()
    {
        $game = new Game(self::A_GAME_NAME, self::A_GAME_DESCRIPTION);
        $game->setName(self::ANOTHER_GAME_NAME);
        $game->setDescription(self::ANOTHER_GAME_DESCRIPTION);
    }
}
