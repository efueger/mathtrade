<?php
namespace Edysanchez\Mathtrade\Application\Service\BoardGameGeekImport;

class BoardGameGeekImportRequest
{
    private $username;

    public function __construct($username)
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function username()
    {
        return $this->username;
    }
}
