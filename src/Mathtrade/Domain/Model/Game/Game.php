<?php

namespace Edysanchez\Mathtrade\Domain\Model\Game;

class Game
{
    public $id;
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $description;

    /**
     * @var string
     */
    public $thumbnail;

    /**
     * @var int
     */
    public $boardGameGeekId;

    /**
     * @var int
     */
    public $collectionId;

    /**
     * @var int
     */
    public $userId;

    public function __construct($id, $name, $userId)
    {
        $this->id = $id;
        $this->name = $name;
        $this->userId = $userId;
    }

    /**
     * @return string
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function id()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function collectionId()
    {
        return $this->collectionId;
    }

    /**
     * @param int $collectionId
     */
    public function setCollectionId($collectionId)
    {
        $this->collectionId = $collectionId;
    }

    /**
     * @return int
     */
    public function boardGameGeekId()
    {
        return $this->boardGameGeekId;
    }

    /**
     * @param int $boardGameGeekId
     */
    public function setBoardGameGeekId($boardGameGeekId)
    {
        $this->boardGameGeekId = $boardGameGeekId;
    }

    /**
     * @return string
     */
    public function thumbnail()
    {
        return $this->thumbnail;
    }

    /**
     * @param string $thumbnail
     */
    public function setThumbnail($thumbnail)
    {
        $this->thumbnail = $thumbnail;
    }

    /**
     * @return string
     */
    public function description()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function userId()
    {
        return $this->userId;
    }
}
