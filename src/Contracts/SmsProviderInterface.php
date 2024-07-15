<?php

namespace Ijeyg\Larapayamak\Services;

interface SmsProviderInterface
{
    public function send($phoneNumber, $message);
}
