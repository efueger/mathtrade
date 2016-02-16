<?php

namespace Edysanchez\Mathtrade\Application\Service\GetAllMathtradeItems;

use Edysanchez\Mathtrade\Domain\Model\MathtradeItem\MathtradeItem;
use Edysanchez\Mathtrade\Domain\Model\MathtradeItem\MathtradeItemRepository;

class GetAllMathtradeItemsUseCase
{
    /**
     * @var MathtradeItemRepository
     */
    private $itemsRepository;

    /**
     * GetAllItemsUseCase constructor.
     * @param MathtradeItemRepository $itemsRepository
     */
    public function __construct(MathtradeItemRepository $itemsRepository)
    {
        $this->itemsRepository = $itemsRepository;
    }

    /**
     * @return GetAllMathtradeItemsResponse
     */
    public function execute()
    {
        $response = new GetAllMathtradeItemsResponse();
        $items = $this->itemsRepository->findAll();
        foreach ($items as $item) {
            $newItem = $this->makePlainGame($item);
            $response->items[] = $newItem;
        }

        return $response;
    }

    /**
     * @param $item
     * @return array
     */
    protected function makePlainGame(MathtradeItem $item)
    {
        $games = $item->games();
        $plaingames = array();
        foreach ($games as $game) {
            $plainGame = array(
                'id' => $game->id(),
                'name' => $game->name(),
                'description' => $game->description(),
                'bgg_id' => $game->boardGameGeekId(),
                'collid' => $game->collectionId(),
                'bgg_img' => $game->thumbnail(),
                'account_id' => $game->userId()
            );
            $plaingames[] = $plainGame;
        }
        $newItem = array(
            'id' => $item->id(),
            'games' => $plaingames
        );

        return $newItem;
    }
}
