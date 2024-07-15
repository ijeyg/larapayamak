<?php

namespace Ijeyg\Larapayamak\Services;

use Ijeyg\Larapayamak\Contracts\SmsProviderInterface;

class SmsirProvider implements SmsProviderInterface
{
    private HttpClientService $httpClientService;
    private mixed $username;
    private mixed $line;
    private mixed $token;

    private string $baseUrl = 'https://api.sms.ir/v1/';

    public function __construct(HttpClientService $httpClientService, $username, $line, $token)
    {
        $this->httpClientService = $httpClientService;
        $this->username = $username;
        $this->line = $line;
        $this->token = $token;
    }

    public function sendSimpleMessage($phoneNumber, $message)
    {
        $data = [
            'password' => $this->token,
            'username' => $this->username,
            'line' => $this->line,
            'mobile' => $phoneNumber,
            'text' => $message,
        ];
        $header = [
            'Accept' => 'application/json',
            'X-API-KEY' => $this->token,
        ];
        try {
            return $this->httpClientService->connectViaGet($this->baseUrl . 'send', $data , $header);
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    /**
     * @param array $parameters
     * @return array
     */
    private function setParameters(array $parameters): array
    {
        $array = null;
        foreach ($parameters as $key => $value) {
            $array[] = ['Name' => $key , 'Value'=> $value];
        }
        return $array;
    }
}
