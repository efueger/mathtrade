<?php

namespace Edysanchez\Mathtrade\Domain\Model\Game;

class Game
{
    protected $id;
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $thumbnail;

    /**
     * @var int
     */
    protected $boardGameGeekId;

    /**
     * @var int
     */
    protected $collectionId;

    protected $userId;

    public function __construct($id, $name)
    {
        $this->id = $id;
        $this->name = $name;
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

    /**
     * @param mixed $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }
}
