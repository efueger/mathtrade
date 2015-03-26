<?php

namespace Mathtrade\Infrastructure\Persistence\InMemory\Game;

use Mathtrade\Domain\Model\Game;

class InMemoryGameRepositoryTest extends \PHPUnit_Framework_TestCase
{

    const A_GAME_ID = 0;
    const A_GAME_NAME = 'game';
    const A_GAME_DESCRIPTION = 'description';

    /** @var  GameRepository */
    protected $repo;

    public function  setUp() {

        $this->repo = new InMemoryGameRepository();
    }
    public function testFindEmptyRepositoryShouldReturnNull()
    {

        $this->assertNull($this->repo->find(self::A_GAME_ID));
    }

    public function testFindGameNotInRepositoryShouldReturnNull()
    {
        $this->assertNull($this->repo->find(self::A_GAME_ID));
    }

    public function testFindGameInRepositoryShouldReturnGme()
    {
        /** @var  Game */
        $game=new Game(self::A_GAME_NAME, self::A_GAME_DESCRIPTION);

        $this->repo->persist($game);
        /** @var Game */
        $anotherGame = $this->repo->find(self::A_GAME_ID);
        $this->assertNotNull($anotherGame);
        $this->assertEquals($game->name(),$anotherGame->name());
    }

    public function testUpdateGameInRepositoryShouldChange()
    {
        /** @var  Game */
        $game=new Game(self::A_GAME_NAME, self::A_GAME_DESCRIPTION);

        $this->repo->persist($game);



        /** @var  Game */
        $game= $this->repo->find($game->id());

        $game->setName('cucu');

        $this->repo->persist($game);

        /** @var Game */
        $game=$this->repo->find($game->id());

        $this->assertEquals($game->name(),'cucu');
    }
}
