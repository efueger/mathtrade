<?php


namespace Edysanchez\Mathtrade\Domain\Model\WildCard;



use Edysanchez\Mathtrade\Domain\Model\MathtradeItem\MathtradeItem;

class WildCard
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var int
     */
    private $userId;

    /**
     * @var MathtradeItem[]
     */
    private $games;
    
    private $id;

    public function __construct($id, $name, $userId, $games)
    {
        $this->name = $name;
        $this->userId = $userId;
        $this->games = $games;
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function id()
    {
        return $this->id;
    }

    /**
     * @return \Edysanchez\Mathtrade\Domain\Model\MathtradeItem\MathtradeItem[]
     */
    public function games()
    {
        return $this->games;
    }

    /**
     * @return int
     */
    public function userId()
    {
        return $this->userId;
    }


}