<?php

namespace Ijeyg\Larapayamak\Contracts;

use Illuminate\Http\JsonResponse;

interface SmsProviderInterface
{
    public function sendSimpleMessage($phoneNumber, $message): JsonResponse;

    public function sendPatternMessage($phoneNumber,$pattern,$parameters): JsonResponse;

}
