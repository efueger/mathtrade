<?php

namespace Edysanchez\Mathtrade\Infrastructure\Persistence\InMemory\Game;


use Edysanchez\Mathtrade\Domain\Model\Game\Game;

class InMemoryGameRepositoryTest extends \PHPUnit_Framework_TestCase
{

    const A_GAME_ID = 0;
    const A_GAME_NAME = 'game';
    const A_GAME_DESCRIPTION = 'description';
    const A_USER_NAME = 'user';
    const USER_NAME_WITH_GAMES = 'userGames';
    const A_BGG_ID = 12;
    const A_DESCRIPTION = 'A description';
    const COLLECTION_ID = 54;
    const THUMBNAIL = 'thumbnail';

    /** @var InMemoryGameRepository */
    protected $repo;

    public function  setUp() {

        /** @var  Game */
        $game=new Game(self::A_GAME_ID, self::A_GAME_NAME);
        $game->setBoardGameGeekId(self::A_BGG_ID);
        $game->setDescription(self::A_DESCRIPTION);
        $game->setCollectionId(self::COLLECTION_ID);
        $game->setThumbnail(self::THUMBNAIL);
        $repo = array();
        $repo[self::USER_NAME_WITH_GAMES] = array($game);

        $this->repo = new InMemoryGameRepository($repo);
    }

    /**
     * @test
     */
    public function findEmptyRepositoryShouldReturnNull()
    {
        $this->setExpectedException('\Exception');
        $this->assertEmpty($this->repo->findByUsername(self::A_USER_NAME));
    }

    /**
     * @test
     */
    public function findUserWithoutGamesInRepositoryShouldReturnNull()
    {
        $this->setExpectedException('\Exception');
        $this->assertEmpty($this->repo->findByUsername(self::A_USER_NAME));
    }

    /**
     * @test
     */
    public function findGameInRepositoryShouldReturnGme()
    {

        /** @var Game[] */
        $games = $this->repo->findByUsername(self::USER_NAME_WITH_GAMES);
        $this->assertNotEmpty($games);
        $this->assertEquals($games[0]->name(), self::A_GAME_NAME);
        $this->assertEquals($games[0]->id(), self::A_GAME_ID);
        $this->assertEquals($games[0]->boardGameGeekId(), self::A_BGG_ID);
        $this->assertEquals($games[0]->thumbnail(), self::THUMBNAIL);
        $this->assertEquals($games[0]->collectionId(), self::COLLECTION_ID);
        $this->assertEquals($games[0]->description(), self::A_DESCRIPTION);
    }

}
