<?php

namespace Edysanchez\Mathtrade\Infrastructure\Persistence\XmlApi\Game;

use Edysanchez\Mathtrade\Domain\Model\Game\Game;
use Edysanchez\Mathtrade\Domain\Model\Game\GameRepository;
use Exception;
use Guzzle\Http\Client;

class BoardGameGeekRepository implements GameRepository
{
    /**
    * @param $username
    * @return Game []
    */
    public function findByUsername($username)
    {
        define('USER_TRADE_REQUEST', 'http://boardgamegeek.com/xmlapi2/collection?username=' . $username . '&trade=1');

        $bggClient = new Client();

        $queryRequest = $bggClient->get(USER_TRADE_REQUEST);
        $queryResponse = $queryRequest->send();

        $this->guardFromHTTPError($queryResponse);

        if($queryResponse->getStatusCode() === 202) {
            $queryResponse = $queryRequest->send();
        }
        $this->guardFromHTTPError($queryResponse);
        $xml = $queryResponse->xml();

        $this->guardFromApiError($xml);

        $games = array();

        foreach($xml->children() as $gameNode) {
            $id = uniqid();
            $game = new Game((int)$id, (string)$gameNode->name);
            $game->setThumbnail((string)$gameNode->thumbnail);
            $game->setDescription((string)$gameNode->textCondition);
            $attributes = $gameNode->attributes();
            $game->setBoardGameGeekId((int)$attributes['objectid']);
            $game->setCollectionId((int)$attributes['collid']);

            $games[] = $game;
        }

        return $games;
    }

    /**
     * @param $xml
     * @throws Exception
     */
    protected function guardFromApiError($xml)
    {
        if ($xml->error) {
            throw new \Exception('Error ');
        }
    }

    /**
     * @param $queryResponse
     * @throws Exception
     */
    protected function guardFromHTTPError($queryResponse)
    {
        if ($queryResponse->getStatusCode() >= 400) {
            throw new \Exception('Api Error');
        }
    }

    /**
     * @param $queryResponse
     * @throws Exception
     */
    private function guardFromQueryError($queryResponse)
    {
        $statusCode = $queryResponse->getStatusCode();
        if ($statusCode !== 202 && $statusCode !== 200) {
            throw new Exception('Error in query');
        }
    }
    /**
     * @param $dataResponse
     * @throws Exception
     */
    private function guardFromErrorGettingData($dataResponse)
    {
        if ($dataResponse->getStatusCode() !== 200) {
            throw new Exception('Error getting data');
        }
    }
}