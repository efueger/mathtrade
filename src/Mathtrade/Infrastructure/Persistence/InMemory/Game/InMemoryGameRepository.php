<?php

namespace Edysanchez\Mathtrade\Infrastructure\Persistence\InMemory\Game;

use Edysanchez\Mathtrade\Domain\Model\Game\Game;
use Edysanchez\Mathtrade\Domain\Model\Game\GameRepository;

class InMemoryGameRepository implements GameRepository
{
    protected $repo;
    protected $nextId=0;

    public function __construct($repo)
    {
        $this->repo = $repo;
    }

    /**
     * @param string
     * @return Game[]
     */
    public function findByUsername($username)
    {
        $res = array();
        foreach($this->repo as $user => $games) {
            if($user == $username) {
                $res[] = $games;
            }
        }
        return $res;
    }
}
