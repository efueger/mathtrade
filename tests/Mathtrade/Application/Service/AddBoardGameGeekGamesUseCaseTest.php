<?php


namespace Edysanchez\Mathtrade\Test\Application\Service;

use Edysanchez\Mathtrade\Application\Service\AddBoardGameGeekGames\AddBoardGameGeekGamesRequest;
use Edysanchez\Mathtrade\Application\Service\AddBoardGameGeekGames\AddBoardGameGeekGamesUseCase;
use Edysanchez\Mathtrade\Domain\Model\Game\Game;
use Edysanchez\Mathtrade\Domain\Model\Game\GameRepository;
use Edysanchez\Mathtrade\Infrastructure\Persistence\InMemory\Game\InMemoryGameRepository;

class AddBoardGameGeekGamesUseCaseTest extends \PHPUnit_Framework_TestCase
{
    const A_GAME_ID = 1;
    const A_GAME_NAME = 'game';
    const A_GAME_DESCRIPTION = 'a game';

    const A_COLLECTION_ID = 12;
    const A_USER_NAME = 'username';

    const ANOTHER_GAME_ID = 2;
    const ANOTHER_GAME_NAME = 'another game name';
    const ANOTHER_COLLECTION_ID = 13;

    /**
     * @var Game
     */
    private $game;
    /**
     * @var AddBoardGameGeekGamesRequest
     */
    private $addBoardGameGeekGamesRequest;

    /**
     * @var AddBoardGameGeekGamesUseCase
     */
    private $addBoardGameGeekGamesUseCase;
    /**
     * @var GameRepository
     */
    private $gameRepo;

    /**
     * @var InMemoryGameRepository
     */
    private $gameRepository;

    private $anotherGame;

    protected function setUp()
    {
        $this->game = new Game(self::A_GAME_ID, self::A_GAME_NAME, self::A_GAME_DESCRIPTION);
        $game = $this->game;
        $game->setCollectionId(self::A_COLLECTION_ID);
        $this->anotherGame = new Game(self::ANOTHER_GAME_ID, self::ANOTHER_GAME_NAME, self::A_GAME_DESCRIPTION);
        $this->anotherGame->setCollectionId(self::ANOTHER_COLLECTION_ID);
        $this->gameRepository = $this->createRepo(array($game));
    }

    /**
     * @test
     */
    public function givenAUserWithGameImportedWhenAddTheGameThenTheUserShouldNotHaveMoreGames()
    {

        $game= $this->makePlainGame($this->game);

        $this->addBoardGameGeekGamesRequest = new AddBoardGameGeekGamesRequest(self::A_USER_NAME,[$game]);
        $this->addBoardGameGeekGamesUseCase = new AddBoardGameGeekGamesUseCase($this->gameRepository);
        $this->addBoardGameGeekGamesUseCase->execute($this->addBoardGameGeekGamesRequest);
        $this->assertEquals(1, count($this->gameRepository->findByUserName(self::A_USER_NAME))) ;

    }

    /**
     * @test
     */
    public function givenAUserWithoutGameImportedWhenAddTheGameThenTheUserShouldHaveMoreGames()
    {
        $anotherGame= $this->makePlainGame($this->anotherGame);

        $this->addBoardGameGeekGamesRequest = new AddBoardGameGeekGamesRequest(self::A_USER_NAME,[$anotherGame]);
        $this->addBoardGameGeekGamesUseCase = new AddBoardGameGeekGamesUseCase($this->gameRepository);
        $this->addBoardGameGeekGamesUseCase->execute($this->addBoardGameGeekGamesRequest);
        $this->assertEquals(2, count($this->gameRepository->findByUserName(self::A_USER_NAME))) ;
    }

    /**
     * @param $games[]
     * @return InMemoryGameRepository
     */
    protected function createRepo($games)
    {
        $repository = new InMemoryGameRepository(array(self::A_USER_NAME => $games));
        return $repository;
    }

    /**
     * @param Game $game
     * @return array
     */
    protected function makePlainGame(Game $game)
    {
        return [
            'name' => $game->name(),
            'bgg_img' => $game->thumbnail(),
            'description' => $game->description(),
            'bgg_id' => $game->boardGameGeekId(),
            'collid' => $game->collectionId(),
            'id' => null,
            'wantname' => null
        ];
    }
}
