<?php

namespace Ijeyg\Larapayamak\Services;

use Illuminate\Support\Facades\Http;

class HttpClientService
{
    /**
     * @param $url
     * @param $parameters
     * @param $headers
     * @return array|mixed
     * @throws \Exception
     */
    public function connectViaGet($url, $parameters = [], $headers = []): mixed
    {
        try {
            return Http::withHeaders($headers)->get($url,$parameters)->json();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param $url
     * @param $parameters
     * @param $headers
     * @return array|mixed
     * @throws \Exception
     */
    public function connectViaPost($url, $parameters = [], $headers = []): mixed
    {
        try {
            return Http::withHeaders($headers)->post($url,$parameters)->json();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode(), $e);
        }
    }
}
