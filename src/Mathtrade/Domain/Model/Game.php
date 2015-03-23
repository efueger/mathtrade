<?php

namespace Mathtrade\Domain\Model;

class Game
{
    protected $gameId;
    protected $name;
    protected $description;

    public function __construct($name, $description)
    {
        $this->name = $name;
        $this->description = $description;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function name()
    {
        return $this->name;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function description()
    {
        return $this->description;
    }
}
