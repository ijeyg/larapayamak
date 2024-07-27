<?php

namespace Ijeyg\Larapayamak\Services;

use Ijeyg\Larapayamak\Contracts\SmsProviderInterface;
use Illuminate\Http\JsonResponse;

class SmsService
{
    protected SmsProviderInterface $provider;

    public function __construct(SmsProviderInterface $provider)
    {
        $this->provider = $provider;
    }

    public function sendSimpleMessage($phoneNumber, $message): JsonResponse
    {
        return $this->provider->sendSimpleMessage($phoneNumber, $message);
    }

    public function sendPatternMessage($phoneNumber,$pattern,$parameters): JsonResponse
    {
        return $this->provider->sendPatternMessage($phoneNumber,$pattern,$parameters);
    }
}
