<?php

namespace Edysanchez\Mathtrade\Infrastructure\Persistence\XmlApi\Game;

use Edysanchez\Mathtrade\Domain\Model\Game\BoardGameGeekSearchableRepository;
use Edysanchez\Mathtrade\Domain\Model\Game\Game;
use Exception;
use Guzzle\Http\Client;

class BoardGameGeekRepository implements BoardGameGeekSearchableRepository
{
    /**
    * @param $username
    * @return Game []
    */
    public function findTradeableByUsername($username)
    {
        define('USER_TRADE_REQUEST', 'http://boardgamegeek.com/xmlapi2/collection?username=' . $username . '&trade=1');

        $data = $this->getData();

        $games = array();

        foreach ($data as $gameNode) {

            $game = $this->makeGame($gameNode);

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
     * @param $queryRequest
     * @return mixed
     */
    protected function tryToRepeatIfWaitReply($queryResponse, $queryRequest)
    {
        if ($queryResponse->getStatusCode() === 202) {
            $queryResponse = $queryRequest->send();
            return $queryResponse;
        }
        return $queryResponse;
    }

    /**
     * @param $gameNode
     * @return Game
     */
    protected function makeGame($gameNode)
    {
        $id = uniqid();
        $game = new Game((int)$id, (string)$gameNode->name);
        $game->setThumbnail((string)$gameNode->thumbnail);
        $game->setDescription((string)$gameNode->conditiontext);
        $attributes = $gameNode->attributes();
        $game->setBoardGameGeekId((int)$attributes['objectid']);
        $game->setCollectionId((int)$attributes['collid']);
        return $game;
    }

    /**
     * @return mixed
     * @throws Exception
     */
    protected function getData()
    {
        $bggClient = new Client();

        $queryRequest = $bggClient->get(USER_TRADE_REQUEST);
        $queryResponse = $queryRequest->send();

        $this->guardFromHTTPError($queryResponse);

        $queryResponse = $this->tryToRepeatIfWaitReply($queryResponse, $queryRequest);

        $this->guardFromHTTPError($queryResponse);
        $xml = $queryResponse->xml();

        $this->guardFromApiError($xml);
        return $xml->children();
    }
}
