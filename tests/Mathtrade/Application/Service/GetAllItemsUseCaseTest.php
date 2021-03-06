<?php

namespace Edysanchez\Mathtrade\Test\Application\Service;


use Edysanchez\Mathtrade\Application\Service\GetAllItems\GetAllItemsUseCase;
use Edysanchez\Mathtrade\Domain\Model\Item\Item;
use Edysanchez\Mathtrade\Infrastructure\Persistence\InMemory\Item\InMemoryItemRepository;

class GetAllItemsUseCaseTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var InMemoryItemRepository
     */
    private $inMemoryItemRepository;
    
    protected function setUp()
    {
        $this->inMemoryItemRepository = new InMemoryItemRepository();

    }

    /**
     * @test
     */
    public function GivenAnEmptyGameRepositoryWhenGettingAllItemsThenShouldReturnNothing()
    {
        $useCase = new GetAllItemsUseCase($this->inMemoryItemRepository);
        $response = $useCase->execute();
        $this->assertEquals(0,count($response->items));
    }

    /**
     * @test
     */
    public function GivenANonEmptyGameRepositoryWhenGettingAllItsItemsThenReturnAllTheItems()
    {
        $this->inMemoryItemRepository->add(new Item(44, "item1",null ,"user1"));
        $this->inMemoryItemRepository->add(new Item(45, "item2",null ,"user2"));
        $useCase = new GetAllItemsUseCase($this->inMemoryItemRepository);
        $response = $useCase->execute();
        $this->assertEquals(2,count($response->items));
    }
}