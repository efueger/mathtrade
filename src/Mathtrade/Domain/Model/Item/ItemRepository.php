<?php

namespace Edysanchez\Mathtrade\Domain\Model\Item;

interface ItemRepository
{
    /**
     * @param $id
     * @return Item
     */
    public function find($id);

    /**
     * @return Item[]
     */
    public function findAll();
}
