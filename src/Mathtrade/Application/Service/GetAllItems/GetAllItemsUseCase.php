<?php

namespace Edysanchez\Mathtrade\Application\Service\GetAllItems;

use Edysanchez\Mathtrade\Domain\Model\Item\ItemRepository;

class GetAllItemsUseCase
{
    private $itemsRepository;

    /**
     * GetAllItemsUseCase constructor.
     * @param ItemRepository $itemsRepository
     */
    public function __construct(ItemRepository $itemsRepository){
        $this->itemsRepository = $itemsRepository;
    }

    /**
     * @return GetAllItemsResponse
     */
    public function execute()
    {
        $response = new GetAllItemsResponse();
        $items = $this->itemsRepository->findAll();

        foreach($items as $item) {
            $newItem = array(
                'id' => $item->id(),
                'img' => $item->img(),
                'name' => $item->name(),
                'user_name' => $item->userName()
            );
            $response->items[] = $newItem;
        }
        return $response;
    }
}