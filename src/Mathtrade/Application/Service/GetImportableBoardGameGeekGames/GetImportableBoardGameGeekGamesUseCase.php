<?php
namespace Edysanchez\Mathtrade\Application\Service\GetImportableBoardGameGeekGames;

use Edysanchez\Mathtrade\Domain\Model\Game\BoardGameGeekSearchableRepository;
use Edysanchez\Mathtrade\Domain\Model\Game\GameRepository;

class GetImportableBoardGameGeekGamesUseCase
{
    /**
     * @var GameRepository
     */
    private $repository;

    public function __construct(BoardGameGeekSearchableRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param  GetImportableBoardGameGeekGamesRequest  $request
     * @return GetImportableBoardGameGeekGamesResponse
     */

    public function execute(GetImportableBoardGameGeekGamesRequest $request)
    {
        $games = $this->repository->findTradeableByUsername($request->username());

        $response = array();
        foreach ($games as $game) {
            $responseGame = array();
            $responseGame['name'] = $game->name();
            $responseGame['bgg_img'] = $game->thumbnail();
            $responseGame['description'] = $game->description();
            $responseGame['bgg_id'] = $game->boardGameGeekId();
            $responseGame['collid'] = $game->collectionId();

            $response[] = $responseGame;
        }

        return new GetImportableBoardGameGeekGamesResponse($response);
    }
}
