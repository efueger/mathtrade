<?php

namespace Edysanchez\Mathtrade\Infrastructure\Persistence\InMemory\Game;

use Edysanchez\Mathtrade\Domain\Model\Game\Game;
use Edysanchez\Mathtrade\Domain\Model\Game\GameRepository;
use Exception;

class InMemoryGameRepository implements GameRepository
{
    protected $repo;
    protected $nextId=0;

    public function __construct($repo)
    {
        $this->repo = $repo;
    }

    /**
     * @param $username
     * @return array|\Edysanchez\Mathtrade\Domain\Model\Game\Game[]
     * @throws Exception
     */
    public function findByUsername($username)
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
