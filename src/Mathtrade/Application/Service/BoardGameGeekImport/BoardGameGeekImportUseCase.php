<?php
namespace Edysanchez\Mathtrade\Application\Service\BoardGameGeekImport;

use Edysanchez\Mathtrade\Domain\Model\Game\GameRepository;

class BoardGameGeekImportUseCase
{
    /**
     * @var GameRepository
     */
    private $repository;

    public function __construct(GameRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param BoardGameGeekImportRequest $request
     * @return BoardGameGeekImportResponse
     */

    public function execute(BoardGameGeekImportRequest $request) {
        $games = $this->repository->findByUsername($request->username());

        $response = array();
        foreach($games as $game) {
            $responseGame = array();
            $responseGame['name'] = $game->name();
            $responseGame['bgg_img'] = $game->thumbnail();
            $responseGame['description'] = $game->description();
            $responseGame['bgg_id'] = $game->boardGameGeekId();
            $responseGame['collid'] = $game->collectionId();

            $response[] = $responseGame;
        }

        return new BoardGameGeekImportResponse($response);

    }
}