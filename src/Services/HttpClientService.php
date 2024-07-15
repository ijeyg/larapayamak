<?php

namespace Ijeyg\Larapayamak\Utils;

use GuzzleHttp\Exception\GuzzleException;

class Client
{
    /**
     * @param $url
     * @param $parameters
     * @return string
     * @throws \Exception
     */
    public function connectViaPost($url, $parameters = []): string
    {
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->request('POST', $url, $parameters);
            return $response->getBody()->getContents();
        } catch (GuzzleException $e) {
            throw new \Exception($e->getMessage(), $e->getCode(), $e);
        }
    }
}
