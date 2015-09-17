<?php

namespace EdySanchez\Mathtrade\Test\Application\Service;


use Edysanchez\Mathtrade\Application\Service\GetAllItems\GetAllItemsRequest;
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
    public function whenNoHavingItemsShouldNotReturn()
    {
        $useCase = new GetAllItemsUseCase($this->inMemoryItemRepository);
        $response = $useCase->execute();
        $this->assertEquals(0,count($response->items));
    }

    /**
     * @test
     */
    public function whenHavingItemsShouldReturnAllTheItems()
    {
        $this->inMemoryItemRepository->add(new Item(44));
        $this->inMemoryItemRepository->add(new Item(45));
        $useCase = new GetAllItemsUseCase($this->inMemoryItemRepository);
        $response = $useCase->execute();
        $this->assertEquals(2,count($response->items));
    }
}