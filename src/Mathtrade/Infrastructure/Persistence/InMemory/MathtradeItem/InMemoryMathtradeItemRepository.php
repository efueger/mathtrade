<?php
namespace Edysanchez\Mathtrade\Infrastructure\Persistence\InMemory\MathtradeItem;


use Edysanchez\Mathtrade\Domain\Model\MathtradeItem\MathtradeItem;
use Edysanchez\Mathtrade\Domain\Model\MathtradeItem\MathtradeItemRepository;

class InMemoryMathtradeItemRepository implements MathtradeItemRepository
{
    /**
     * @var MathtradeItem[]
     */
    private $repository;

    public function __construct()
    {
        $this->repository = array();
    }

    /**
     * @param $id
     * @return MathtradeItem|null
     */
    public function find($id)
    {
        foreach ($this->repository as $item) {
            if ($item->id() === $id) {
                return $item;
            }
        }
        return null;
    }

    /**
     * @param MathtradeItem $item
     */
    public function add(MathtradeItem $item)
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
}
