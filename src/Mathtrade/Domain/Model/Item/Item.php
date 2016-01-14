<?php

namespace Edysanchez\Mathtrade\Domain\Model\Item;

class Item
{
    private $id;
    private $name;
    private $img;
    private $userName;

    public function __construct($id, $name, $img, $userName)
    {
        $this->id = $id;
        $this->name = $name;
        $this->img = $img;
        $this->userName=$userName;
    }

    public function id()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function userName()
    {
        return $this->userName;
    }

    /**
     * @return mixed
     */
    public function img()
    {
        return $this->img;
    }

    /**
     * @return mixed
     */
    public function name()
    {
        return $this->name;
    }
}
