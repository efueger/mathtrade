<?php


namespace Edysanchez\Mathtrade\Application\Service\ExportMathtradeData;


class ExportMathtradeDataResponse
{
    private $exportData;

    public function __construct($exportData)
    {

        $this->exportData = $exportData;
    }

    /**
     * @return mixed
     */
    public function exportData()
    {
        return $this->exportData;
    }
}
