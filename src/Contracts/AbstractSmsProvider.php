<?php

namespace Ijeyg\Larapayamak\Contracts;

use Ijeyg\Larapayamak\Services\HttpClientService;
use Illuminate\Http\JsonResponse;

abstract class AbstractSmsProvider implements SmsProviderInterface
{
    protected HttpClientService $httpClientService;

    public function setHttpClient(HttpClientService $httpClientService): void
    {
        $this->httpClientService = $httpClientService;
    }

    abstract public function sendSimpleMessage($phoneNumber, $message): JsonResponse;

    abstract public function sendPatternMessage($phoneNumber, $pattern, $parameters): JsonResponse;
}
