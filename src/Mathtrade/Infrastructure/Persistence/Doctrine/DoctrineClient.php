<?php


namespace Edysanchez\Mathtrade\Infrastructure\Persistence\Doctrine;


class DoctrineClient
{
    private $dbName;
    private $userName;
    private $password;
    private $host;
    private $driver;

    public function __construct($dbName, $userName, $password, $host, $driver)
    {

        $this->dbName = $dbName;
        $this->userName = $userName;
        $this->password = $password;
        $this->host = $host;
        $this->driver = $driver;
    }

    /**
     * @return mixed
     */
    public function host()
    {
        return $this->host;
    }

    /**
     * @return mixed
     */
    public function dbName()
    {
        return $this->dbName;
    }

    /**
     * @return mixed
     */
    public function password()
    {
        return $this->password;
    }

    /**
     * @return mixed
     */
    public function driver()
    {
        return $this->driver;
    }

    /**
     * @return mixed
     */
    public function userName()
    {
        return $this->userName;
    }
}
