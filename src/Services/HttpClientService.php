<?php

namespace Ijeyg\Larapayamak\Services;

use Illuminate\Support\Facades\Http;

class HttpClientService
{
    /**
     * @param $url
     * @param array $parameters
     * @param array $headers
     * @return array|mixed
     * @throws \Exception
     */
    public function connectViaGet($url, array $parameters = [], array $headers = []): mixed
    {
        try {
            return Http::withHeaders($headers)->get($url,$parameters)->json();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param $url
     * @param array $parameters
     * @param array $headers
     * @return array|mixed
     * @throws \Exception
     */
    public function connectViaPost($url, array $parameters = [], array $headers = []): mixed
    {
        try {
            return Http::withHeaders($headers)->post($url,$parameters)->json();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode(), $e);
        }
    }
}
