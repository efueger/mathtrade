<?php

namespace Edysanchez\Test\Mathtrade\Infrastructure\Persistence\InMemory\Item;

use Edysanchez\Mathtrade\Domain\Model\Item\Item;
use Edysanchez\Mathtrade\Domain\Model\Item\ItemRepository;
use Edysanchez\Mathtrade\Infrastructure\Persistence\InMemory\Item\InMemoryItemRepository;

class InMemoryItemRepositoryTest extends \PHPUnit_Framework_TestCase
{
    const AN_ID = 1;
    const A_NAME = 'name';
    const AN_IMAGE = 'bgg_img';
    const AN_USER_NAME = 'userName';
    const ANOTHER_ID = 2;

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
        $item = new Item($id, self::A_NAME, self::AN_IMAGE, self::AN_USER_NAME);
        $this->repository->add($item);
        $item = new Item(self::ANOTHER_ID, self::A_NAME, self::AN_IMAGE, self::AN_USER_NAME);
        $this->repository->add($item);

        $this->assertEquals(2,count($this->repository->findAll()));

    }

    /**
     * @test
     */
    public function itShouldReturnAnExistingItem()
    {
        $id = self::AN_ID;
        $item = new Item($id, self::A_NAME, self::AN_IMAGE, self::AN_USER_NAME);
        $this->repository->add($item);

        $this->assertNotNull($this->repository->findById($id));

    }
}
