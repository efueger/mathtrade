<?php

namespace Edysanchez\Mathtrade\Test\Application\Service;


use Edysanchez\Mathtrade\Application\Service\GetAllMathtradeItems\GetAllMathtradeItemsUseCase;
use Edysanchez\Mathtrade\Domain\Model\Game\Game;
use Edysanchez\Mathtrade\Domain\Model\MathtradeItem\MathtradeItem;
use Edysanchez\Mathtrade\Infrastructure\Persistence\InMemory\MathtradeItem\InMemoryMathtradeItemRepository;

class GetAllMathtradeItemsUseCaseTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var InMemoryMathtradeItemRepository
     */
    private $inMemoryItemRepository;
    
    protected function setUp()
    {
        $this->inMemoryItemRepository = new InMemoryMathtradeItemRepository();

    }

    /**
     * @test
     */
    public function GivenAnEmptyGameRepositoryWhenGettingAllItemsThenShouldReturnNothing()
    {
        $useCase = new GetAllMathtradeItemsUseCase($this->inMemoryItemRepository);
        $response = $useCase->execute();
        $this->assertEquals(0,count($response->items));
    }

    /**
     * @test
     */
    public function GivenANonEmptyGameRepositoryWhenGettingAllItsItemsThenReturnAllTheItems()
    {
        $game = new Game(23, 'game');
        $game->setUserId(25);
        $this->inMemoryItemRepository->add(new MathtradeItem(44, $game));
        $this->inMemoryItemRepository->add(new MathtradeItem(45, new Game(24,'game')));
        $useCase = new GetAllMathtradeItemsUseCase($this->inMemoryItemRepository);
        $response = $useCase->execute();
        $this->assertEquals(2,count($response->items));
        $this->assertEquals(25, $response->items[0]['game']['account_id']);
    }
}