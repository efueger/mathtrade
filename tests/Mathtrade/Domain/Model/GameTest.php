<?php

namespace Mathtrade\Domain\Model;

class GameTest extends \PHPUnit_Framework_TestCase
{

    public function testGameShouldHaveNameAndDescription()
    {
        $game = new Game('name', 'description');
        $this->assertEquals($game->name(), 'name');
        $this->assertEquals($game->description(), 'description');
    }
}
