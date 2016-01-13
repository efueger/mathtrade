<?php

namespace EdySanchez\Mathtrade\Test\Application\Service;


use Edysanchez\Mathtrade\Application\Service\BoardgamegeekImport\BoardGameGeekImportRequest;
use Edysanchez\Mathtrade\Application\Service\BoardGameGeekImport\BoardGameGeekImportUseCase;
use Edysanchez\Mathtrade\Domain\Model\Game\Game;
use Edysanchez\Mathtrade\Domain\Model\Game\GameRepository;
use Edysanchez\Mathtrade\Infrastructure\Persistence\InMemory\Game\InMemoryGameRepository;

class BoardGameGeekImportUseCaseTest extends \PHPUnit_Framework_TestCase
{
    const A_GAME_ID = 1;
    const A_GAME_NAME = 'game';
    const A_GAME_DESCRIPTION = 'description';
    private $boardGameGeekImportUseCase;
    private $repository;

    protected function setUp()
    {
        /**
         * @var GameRepository
         */
        $this->repository = $this->createRepo();
    }

    /**
     * @test
     */
    public function notExistingBoardGameGeekUsernameShouldThrowException()
    {
        $this->setExpectedException('\Exception');
        $boardGameGeekImportRequest = new BoardGameGeekImportRequest('userName');

        $this->boardGameGeekImportUseCase = new BoardGameGeekImportUseCase($this->repository);
        $this->boardGameGeekImportUseCase->execute($boardGameGeekImportRequest);
    }

    /**
     * @test
     */
    public function existingBoardGameGeekUserShouldReturnGames()
    {
        /**
         * @var BoardGameGeekImportRequest
         */
        $boardGameGeekImportRequest = new BoardGameGeekImportRequest('username');
        /**
         * @var BoardGameGeekImportUseCase
         */
        $this->boardGameGeekImportUseCase = new BoardGameGeekImportUseCase($this->repository);
        $response = $this->boardGameGeekImportUseCase->execute($boardGameGeekImportRequest);
        $this->assertNotEmpty($response);
        $games = $response->games();
        $this->assertEquals($games[0]['id'], self::A_GAME_ID);
    }

    /**
     * @return InMemoryGameRepository
     */
    protected function createRepo()
    {
        $game = new Game(self::A_GAME_ID, self::A_GAME_NAME, self::A_GAME_DESCRIPTION);
        $repository = new InMemoryGameRepository(array('username' => array($game)));
        return $repository;
    }
}
