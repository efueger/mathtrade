<?php

namespace Edysanchez\Mathtrade\Application\Service\AddBoardGameGeekGames;

use Edysanchez\Mathtrade\Domain\Model\Game\Game;
use Edysanchez\Mathtrade\Domain\Model\Game\GameRepository;
use Exception;

class AddBoardGameGeekGamesUseCase
{
    /**
     * @var GameRepository
     */
    private $gameRepository;

    public function __construct(GameRepository $gameRepository)
    {

        $this->gameRepository = $gameRepository;
    }

    /**
     * @param  AddBoardGameGeekGamesRequest $addBoardGameGeekGamesRequest
     * @throws Exception
     */
    public function execute(AddBoardGameGeekGamesRequest $addBoardGameGeekGamesRequest)
    {
        $games = $addBoardGameGeekGamesRequest->games();
        foreach ($games as $game) {
            $gameToImport = $this->makeGameToImport($game);

            if (!$this->gameRepository->isGameImportedByUser(
                $addBoardGameGeekGamesRequest->userId(),
                $gameToImport
            )) {
                $this->gameRepository->add($addBoardGameGeekGamesRequest->userId(), $gameToImport);
            }
        }
    }

    /**
     * @param $game
     * @return Game
     */
    protected function makeGameToImport($game)
    {
        $gameToImport = new Game(uniqid(), $game['name']);
        $gameToImport->setBoardGameGeekId($game['bgg_id']);
        $gameToImport->setCollectionId($game['collid']);
        $gameToImport->setThumbnail($game['bgg_img']);
        $gameToImport->setDescription($game['description']);

        return $gameToImport;
    }
}
