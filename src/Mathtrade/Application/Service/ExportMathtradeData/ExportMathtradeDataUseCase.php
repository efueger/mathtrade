<?php


namespace Edysanchez\Mathtrade\Application\Service\ExportMathtradeData;


use Edysanchez\Mathtrade\Domain\Model\MathtradeItem\MathtradeItemRepository;
use Edysanchez\Mathtrade\Domain\Model\WildCard\WildCardRepository;

class ExportMathtradeDataUseCase
{

    /**
     * @var MathtradeItemRepository
     */
    private $itemsRepository;

    /**
     * @var WildCardRepository
     */
    private $wildCardRepository;

    public function __construct(MathtradeItemRepository $itemsRepository, WildCardRepository $wildCardRepository)
    {
        $this->itemsRepository = $itemsRepository;
        $this->wildCardRepository = $wildCardRepository;
    }

    public function execute()
    {
        $items = $this->itemsRepository->findAll() ;
        $data = array();
        $data['games'] = array();
        $data['wildCards'] = array();

        foreach($items as $item) {
            $data['games'][] = $item->game();
        }

        $wildCards = $this->wildCardRepository->findAll();
        foreach($wildCards as $wildCard) {
            $plainWildCard = array();
            $plainWildCard['id'] = $wildCard->id();
            $plainWildCard['user_id'] = $wildCard->userId();
            $plainWildCard['name'] = $wildCard->name();
            $plainWildCard['games'] = array();
            foreach ($wildCard->games() as $game) {
                $plainWildCard['games'][]= $game;
            }
            $data['wildCards'][] = $plainWildCard;
        }

        return new ExportMathtradeDataResponse($data);
    }
}
