<?php

namespace Edysanchez\Mathtrade\Domain\Model\Game;

class Game
{
    protected $id;
    protected $name;

    public function __construct($id,$name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function name()
    {
        return $this->name;
    }

    public function id()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }
}
