<?php


namespace Edysanchez\Mathtrade\Application\Service;


use Edysanchez\Mathtrade\Application\Service\ExportMathtradeData\ExportMathtradeDataUseCase;
use Edysanchez\Mathtrade\Domain\Model\Game\Game;
use Edysanchez\Mathtrade\Domain\Model\MathtradeItem\MathtradeItem;
use Edysanchez\Mathtrade\Infrastructure\Persistence\InMemory\MathtradeItem\InMemoryMathtradeItemRepository;

class ExportMathtradeDataUseCaseTest extends \PHPUnit_Framework_TestCase
{
    private $exportMathtradeDataUseCase;

    private $itemsRepository;
    const A_USER_ID = 44;
    const A_ITEM_ID = 23;
    const A_GAME_ID = 33;

    /**
     * @test
     */
    public function givenEmptyMathtradeWhenExportingDataThenShouldReturnEmptyResponse()
    {
        $this->itemsRepository = new InMemoryMathtradeItemRepository();
        $this->exportMathtradeDataUseCase = new ExportMathtradeDataUseCase($this->itemsRepository);
        $response = $this->exportMathtradeDataUseCase->execute();
        $this->assertEmpty($response->exportData()['games']);
    }

    /**
     * @test
     */
    public function givenMathtradeWithOnlyGamesWhenExportingDataThenShouldReturnOnlyGamesInfo()
    {
        $this->itemsRepository = new InMemoryMathtradeItemRepository();
        $this->itemsRepository->add(new MathtradeItem(self::A_ITEM_ID, new Game(self::A_GAME_ID, 'game', self::A_USER_ID)));
        $this->exportMathtradeDataUseCase = new ExportMathtradeDataUseCase($this->itemsRepository);
        $response = $this->exportMathtradeDataUseCase->execute();
        $this->assertNotEmpty($response->exportData()['games']);
    }
}
