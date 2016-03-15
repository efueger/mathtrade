<?php


namespace Edysanchez\Mathtrade\Infrastructure\Persistence\InMemory\WildCard;


use Edysanchez\Mathtrade\Domain\Model\WildCard\WildCardRepository;

class InMemoryWildCardRepository implements WildCardRepository
{
    private $repo;
    public function __construct($wildcards)
    {
        $this->repo = array();
        if($wildcards) {
            $this->repo = $wildcards;
        }
    }

    public function findAll()
    {
        return $this->repo;
    }
}