<?php

namespace Edysanchez\Mathtrade\Domain\Model\Item;

interface ItemRepository
{

    /**
     * @return Item[]
     */
    public function findAll();
}
