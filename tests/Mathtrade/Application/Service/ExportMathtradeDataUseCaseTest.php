<?php


namespace Edysanchez\Mathtrade\Application\Service;


use Edysanchez\Mathtrade\Application\Service\ExportMathtradeData\ExportMathtradeDataUseCase;
use Edysanchez\Mathtrade\Domain\Model\Game\Game;
use Edysanchez\Mathtrade\Domain\Model\MathtradeItem\MathtradeItem;
use Edysanchez\Mathtrade\Domain\Model\WildCard\WildCard;
use Edysanchez\Mathtrade\Infrastructure\Persistence\InMemory\MathtradeItem\InMemoryMathtradeItemRepository;
use Edysanchez\Mathtrade\Infrastructure\Persistence\InMemory\WildCard\InMemoryWildCardRepository;

class ExportMathtradeDataUseCaseTest extends \PHPUnit_Framework_TestCase
{
    private $exportMathtradeDataUseCase;

    private $itemsRepository;
    const A_USER_ID = 44;
    const A_ITEM_ID = 23;
    const A_GAME_ID = 33;
    private $inMemoryWildCardRepository;
    const A_WILDCARD_NAME = 'test';
    const MATHTRADE_ITEM_ID = 43;

    /**
     * @test
     */
    public function givenEmptyMathtradeWhenExportingDataThenShouldReturnEmptyResponse()
    {
        $this->itemsRepository = new InMemoryMathtradeItemRepository();
        $this->inMemoryWildCardRepository = new InMemoryWildCardRepository(array());
        $this->exportMathtradeDataUseCase = new ExportMathtradeDataUseCase(
            $this->itemsRepository,
            $this->inMemoryWildCardRepository
        );
        $response = $this->exportMathtradeDataUseCase->execute();
        $exportData = $response->exportData();
        $this->assertEmpty($exportData['games']);
    }

    /**
     * @test
     */
    public function givenMathtradeWithOnlyGamesWhenExportingDataThenShouldReturnOnlyGamesInfo()
    {
        $this->itemsRepository = new InMemoryMathtradeItemRepository();
        $this->itemsRepository->add(
            new MathtradeItem(self::A_ITEM_ID, new Game(self::A_GAME_ID, 'game', self::A_USER_ID))
        );
        $this->inMemoryWildCardRepository = new InMemoryWildCardRepository(array());
        $this->exportMathtradeDataUseCase = new ExportMathtradeDataUseCase(
            $this->itemsRepository, $this->inMemoryWildCardRepository
        );
        $response = $this->exportMathtradeDataUseCase->execute();
        $exportData = $response->exportData();
        $this->assertNotEmpty($exportData['games']);
    }

    /**
     * @test
     */
    public function givenMathtradeWithNoItemsAndNoWildcardsWhenExportingThenShouldReturnNothing()
    {
        $this->itemsRepository = new InMemoryMathtradeItemRepository();
        $this->inMemoryWildCardRepository = new InMemoryWildCardRepository(array());

        $this->exportMathtradeDataUseCase = new ExportMathtradeDataUseCase(
            $this->itemsRepository,
            $this->inMemoryWildCardRepository
        );

        $response = $this->exportMathtradeDataUseCase->execute();
        $exportData = $response->exportData();
        $this->assertEmpty($exportData['games']);
        $this->assertEmpty($exportData['wildCards']);
    }

    /**
     * @test
     */
    public function givenMathtradeWithWildCardAndGamesWhenExportingDataThenShouldReturnGamesAndWildCards()
    {
        $this->itemsRepository = new InMemoryMathtradeItemRepository();
        $this->itemsRepository->add(new MathtradeItem(self::MATHTRADE_ITEM_ID, new Game(123,'game', self::A_USER_ID)));
        $this->inMemoryWildCardRepository = new InMemoryWildCardRepository(
            array(new WildCard(1, self::A_WILDCARD_NAME, self::A_USER_ID,array( array(self::MATHTRADE_ITEM_ID))))
        );
        $this->exportMathtradeDataUseCase = new ExportMathtradeDataUseCase(
            $this->itemsRepository,
            $this->inMemoryWildCardRepository
        );
        $response = $this->exportMathtradeDataUseCase->execute();
        $exportData = $response->exportData();
        $this->assertNotEmpty($exportData['games']);
        $this->assertNotEmpty($exportData['wildCards']);
        $this->assertNotEmpty($exportData['wildCards'][0]['games']);
    }

}
