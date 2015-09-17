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
        $response->items = $this->itemsRepository->findAll();
        return $response;
    }
}