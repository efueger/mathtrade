<?php


namespace Edysanchez\Mathtrade\Domain\Model\WildCard;


interface WildCardRepository
{
    /**
     * @return WildCard[]
     */
    public function findAll();
}