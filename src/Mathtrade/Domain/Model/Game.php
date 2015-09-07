<?php

namespace Edysanchez\Mathtrade\Domain\Model;

class Game
{
    protected $id;
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

    public function id()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }
}
