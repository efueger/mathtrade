<?php

namespace Edysanchez\Mathtrade\Test\Application\Service;

use Edysanchez\Mathtrade\Application\Service\GetImportableBoardGameGeekGames\GetImportableBoardGameGeekGamesRequest;
use Edysanchez\Mathtrade\Application\Service\GetImportableBoardGameGeekGames\GetImportableBoardGameGeekGamesUseCase;
use Edysanchez\Mathtrade\Domain\Model\Game\BoardGameGeekSearchableRepository;
use Edysanchez\Mathtrade\Domain\Model\Game\Game;
use Edysanchez\Mathtrade\Domain\Model\Game\GameRepository;
use Edysanchez\Mathtrade\Infrastructure\Persistence\InMemory\Game\InMemoryBoardGameGeekSearchableRepository;
use Edysanchez\Mathtrade\Infrastructure\Persistence\InMemory\Game\InMemoryGameRepository;

class BoardGameGeekGetImportableGamesUseCaseTest extends \PHPUnit_Framework_TestCase
{
    const A_GAME_ID = 1;
    const A_GAME_NAME = 'game';
    const A_GAME_DESCRIPTION = 'description';

    private $boardGameGeekImportUseCase;
    /**
     * @var BoardGameGeekSearchableRepository
     */
    private $repository;

    const A_USER_NAME = 'username';

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
    public function GivenANonExistingUserWhenSearchImportableGamesThenShouldThrowAnException()
    {
        $this->setExpectedException('\Exception');
        $boardGameGeekImportRequest = new GetImportableBoardGameGeekGamesRequest('userName');

        $this->boardGameGeekImportUseCase = new GetImportableBoardGameGeekGamesUseCase($this->repository);
        $this->boardGameGeekImportUseCase->execute($boardGameGeekImportRequest);
    }

    /**
     * @test
     */
    public function GivenUserWithTradeableItemWhenSearchImportableGamesThenShouldReturnGames()
    {
        /**
         * @var GetImportableBoardGameGeekGamesRequest
         */
        $boardGameGeekImportRequest = new GetImportableBoardGameGeekGamesRequest(self::A_USER_NAME);
        /**
         * @var GetImportableBoardGameGeekGamesUseCase
         */
        $this->boardGameGeekImportUseCase = new GetImportableBoardGameGeekGamesUseCase($this->repository);
        $response = $this->boardGameGeekImportUseCase->execute($boardGameGeekImportRequest);
        $this->assertNotEmpty($response->games());
    }

    /**
     * @return InMemoryGameRepository
     */
    protected function createRepo()
    {
        $game = new Game(self::A_GAME_ID, self::A_GAME_NAME, self::A_GAME_DESCRIPTION);
        $repository = new InMemoryBoardGameGeekSearchableRepository(array(self::A_USER_NAME => array($game)));
        return $repository;
    }
}
