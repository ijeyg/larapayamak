<?php

namespace Ijeyg\Larapayamak\Services;

use Ijeyg\Larapayamak\Contracts\SmsProviderInterface;
use Illuminate\Http\JsonResponse;

class SmsService
{
    protected SmsProviderInterface $provider;

    protected GatewayManager $gatewayManager;

    public function __construct(SmsProviderInterface $provider, GatewayManager $gatewayManager)
    {
        $this->provider = $provider;
        $this->gatewayManager = $gatewayManager;
    }

    public function sendSimpleMessage($phoneNumber, $message): JsonResponse
    {
        return $this->provider->sendSimpleMessage($phoneNumber, $message);
    }

    public function sendPatternMessage($phoneNumber, $pattern, $parameters): JsonResponse
    {
        return $this->provider->sendPatternMessage($phoneNumber, $pattern, $parameters);
    }

    public function gateway(string $name): SmsProviderInterface
    {
        return $this->gatewayManager->gateway($name);
    }
}
