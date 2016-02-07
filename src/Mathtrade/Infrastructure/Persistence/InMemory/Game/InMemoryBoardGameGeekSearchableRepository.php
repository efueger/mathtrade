<?php


namespace Edysanchez\Mathtrade\Infrastructure\Persistence\InMemory\Game;

use Edysanchez\Mathtrade\Domain\Model\Game\BoardGameGeekSearchableRepository;
use Exception;

class InMemoryBoardGameGeekSearchableRepository implements BoardGameGeekSearchableRepository
{

    private $repo;

    public function __construct($repo)
    {
        $this->repo = $repo;
    }


    /**
     * @param $username
     * @return array|\Edysanchez\Mathtrade\Domain\Model\Game\Game[]
     * @throws Exception
     */
    public function findTradeableByUsername($username)
    {
        $res = array();
        foreach ($this->repo as $user => $games) {
            if ($user == $username) {
                $res = $games;
            }
        }

        if (count($res) === 0) {
            throw new Exception('Username not found');
        }
        return $res;
    }
}
