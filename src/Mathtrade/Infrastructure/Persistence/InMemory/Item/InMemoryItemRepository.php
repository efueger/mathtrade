<?php
namespace Edysanchez\Mathtrade\Infrastructure\Persistence\InMemory\Item;

use Edysanchez\Mathtrade\Domain\Model\Item\Item;
use Edysanchez\Mathtrade\Domain\Model\Item\ItemRepository;

class InMemoryItemRepository implements ItemRepository
{
    /**
     * @var Item[]
     */
    private $repository;

    public function __construct()
    {
        $this->repository = array();
    }

    /**
     * @param $id
     * @return Item|null
     */
    public function findById($id)
    {
        foreach($this->repository as $item) {
            if($item->id() === $id) {
                return $item;
            }
        }
        return null;
    }

    /**
     * @param Item $item
     */
    public function add(Item $item)
    {
       $this->repository[] = $item;
    }

    /**
     * @return array
     */
    public function findAll()
    {
        return $this->repository;
    }

    /**
     * @param Item $item
     */
    public function save(Item $item)
    {
        throw new \BadMethodCallException();
    }

    /**
     * @param $id
     * @return Item
     */
    public function find($id)
    {
        throw new \BadMethodCallException();
    }
}