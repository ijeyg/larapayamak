<?php

namespace Ijeyg\Larapayamak\Contracts;

use Ijeyg\Larapayamak\Services\HttpClientService;

abstract class SmsProviderInterface
{
    protected HttpClientService $httpClientService;

    protected function __construct()
    {
        $this->httpClientService = new HttpClientService();
    }

    public abstract function sendSimpleMessage($phoneNumber, $message);

    public abstract function sendPatternMessage($phoneNumber,$pattern,$parameters);

}
