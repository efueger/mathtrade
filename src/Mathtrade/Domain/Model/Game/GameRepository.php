<?php

namespace Edysanchez\Mathtrade\Domain\Model\Game;

interface GameRepository
{


    /**
     * @param $username
     * @return Game []
     */
    public function findByUsername($username);
}
