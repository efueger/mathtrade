<?php


namespace Edysanchez\Mathtrade\Application\Service\ExportMathtradeData;


use Edysanchez\Mathtrade\Domain\Model\MathtradeItem\MathtradeItemRepository;

class ExportMathtradeDataUseCase
{

    /**
     * @var MathtradeItemRepository
     */
    private $itemsRepository;

    public function __construct(MathtradeItemRepository $itemsRepository)
    {
        $this->itemsRepository = $itemsRepository;
    }

    public function execute()
    {
        $exportData['games'] = $this->itemsRepository->findAll();
        return new ExportMathtradeDataResponse($exportData);
    }
}
