<?php

namespace Ijeyg\Larapayamak\Services;

use Ijeyg\Larapayamak\Contracts\SmsProviderInterface;
use Ijeyg\Larapayamak\Gateways\FaraPayamak;
use Ijeyg\Larapayamak\Gateways\FarazSms;
use Ijeyg\Larapayamak\Gateways\MeliPayamak;
use Ijeyg\Larapayamak\Gateways\NikSms;
use Ijeyg\Larapayamak\Gateways\PayamResan;
use Ijeyg\Larapayamak\Gateways\Smsir;

class GatewayManager
{
    public function gateway(?string $name = null): SmsProviderInterface
    {
        $gateway = $name ?? config('larapayamak.default');
        $providerConfig = config("larapayamak.gateways.{$gateway}");

        if (is_null($providerConfig)) {
            throw new \InvalidArgumentException("Configuration for the gateway '{$gateway}' not found.");
        }

        switch ($gateway) {
            case 'farapayamak':
                return new FaraPayamak(
                    $providerConfig['username'],
                    $providerConfig['line'],
                    $providerConfig['password']
                );
            case 'melipayamak':
                return new MeliPayamak(
                    $providerConfig['username'],
                    $providerConfig['line'],
                    $providerConfig['password']
                );
            case 'farazsms':
                return new FarazSms(
                    $providerConfig['username'],
                    $providerConfig['password'],
                    $providerConfig['line']
                );
            case 'niksms':
                return new NikSms(
                    $providerConfig['username'],
                    $providerConfig['line'],
                    $providerConfig['password']
                );
            case 'payamresan':
                return new PayamResan(
                    $providerConfig['api_token'],
                );
            case 'smsir':
                return new Smsir(
                    $providerConfig['username'],
                    $providerConfig['line'],
                    $providerConfig['token']
                );
            default:
                throw new \InvalidArgumentException("Unsupported gateway '{$gateway}'.");
        }
    }
}
