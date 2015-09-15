<?php

namespace Edysanchez\Mathtrade\Domain\Model\Item;


class Item
{
    private $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public  function id()
    {
        return $this->id;
    }
}