<?php

namespace Edysanchez\Mathtrade\Domain\Model\Item;

interface ItemRepository
{
    /**
     * @param $id
     * @return Item
     */
    public function findById($id);

    /**
     * @param Item $item
     */
    public function add(Item $item);

    /**
     * @return Item[]
     */
    public function findAll();
}