<?php

namespace Edysanchez\Test\Mathtrade\Infrastructure\Persistence\InMemory\MathtradeItem;

use Edysanchez\Mathtrade\Domain\Model\Game\Game;
use Edysanchez\Mathtrade\Domain\Model\MathtradeItem\MathtradeItem;
use Edysanchez\Mathtrade\Domain\Model\MathtradeItem\MathtradeItemRepository;
use Edysanchez\Mathtrade\Infrastructure\Persistence\InMemory\MathtradeItem\InMemoryMathtradeItemRepository;

class InMemoryMathtradeItemRepositoryTest extends \PHPUnit_Framework_TestCase
{
    const AN_ID = 1;
    const A_NAME = 'name';
    const AN_IMAGE = 'bgg_img';
    const A_USER_ID=33;
    const ANOTHER_ID = 2;
    const A_NAME1 = 'aName';
    const A_GAME_ID = 45;
    const ANOTHER_GAME_NAME = 'anotherGame';
    const ANOTHER_GAME_ID = 46;
    private $game;

    /**
     * @var MathtradeItemRepository
     */
    private $repository;

    protected function setUp()
    {
        $this->repository = new InMemoryMathtradeItemRepository();
    }

    /**
     * @test
     */
    public function itShouldReturnNullIfNotFound()
    {
        $id = self::AN_ID;
        $this->assertEquals(null,$this->repository->find($id));
    }


    /**
     * @test
     */
    public function itShouldReturnAllItems()
    {
        $id = self::AN_ID;
        $item = new MathtradeItem($id, new Game(self::A_GAME_ID, self::A_NAME1, self::A_USER_ID));
        $this->repository->add($item);
        $item = new MathtradeItem(self::ANOTHER_ID, new Game(self::ANOTHER_GAME_ID, self::ANOTHER_GAME_NAME,self::A_USER_ID));
        $this->repository->add($item);

        $this->assertEquals(2,count($this->repository->findAll()));

    }

    /**
     * @test
     */
    public function itShouldReturnAnExistingItem()
    {
        $id = self::AN_ID;
        $this->game = new Game(self::A_GAME_ID, self::A_NAME1, self::A_USER_ID);
        $item = new MathtradeItem($id, $this->game);
        $this->repository->add($item);

        $this->assertNotNull($this->repository->find($id));

    }
}
