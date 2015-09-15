<?php

namespace Edysanchez\Test\Mathtrade\Infrastructure\Persistence\InMemory\Item;

use Edysanchez\Mathtrade\Domain\Model\Item\Item;
use Edysanchez\Mathtrade\Domain\Model\Item\ItemRepository;
use Edysanchez\Mathtrade\Infrastructure\Persistence\InMemory\Item\InMemoryItemRepository;

class InMemoryItemRepositoryTest extends \PHPUnit_Framework_TestCase
{
    const AN_ID = 1;

    /**
     * @var ItemRepository
     */
    private $repository;

    protected function setUp()
    {
        $this->repository = new InMemoryItemRepository();
    }

    /**
     * @test
     */
    public function itShouldReturnNullIfNotFound()
    {
        $id = self::AN_ID;
        $this->assertEquals(null,$this->repository->findById($id));
    }


    /**
     * @test
     */
    public function itShouldReturnAllItems()
    {
        $id = self::AN_ID;
        $item = new Item($id);
        $this->repository->add($item);

        $this->assertEquals(1,count($this->repository->findAll()));

    }

    /**
     * @test
     */
    public function itShouldReturnAnExistingItem()
    {
        $id = self::AN_ID;
        $item = new Item($id);
        $this->repository->add($item);

        $this->assertNotNull($this->repository->findById($id));

    }
}
