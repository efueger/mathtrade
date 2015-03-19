<?php

namespace Mathtrade\Domain\Model;

class GameTest extends \PHPUnit_Framework_TestCase
{

    public function testGameShouldHaveNameAndDescription()
    {
        $game = new Game('name', 'description');
        $this->assertTrue($game->name(), 'name');
        $this->assertTrue($game->description(), 'description');
    }
}
