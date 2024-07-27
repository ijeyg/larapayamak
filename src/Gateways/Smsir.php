<?php

namespace Ijeyg\Larapayamak\Gateways;

use Exception;
use Ijeyg\Larapayamak\Contracts\AbstractSmsProvider;
use Ijeyg\Larapayamak\Services\HttpClientService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class Smsir extends AbstractSmsProvider
{
    private mixed $username;
    private mixed $line;
    private mixed $token;

    private string $baseUrl = 'https://api.sms.ir/v1/';

    public function __construct($username, $line, $token)
    {
        parent::setHttpClient(new HttpClientService());
        $this->username = $username;
        $this->line = $line;
        $this->token = $token;
    }

    public function sendSimpleMessage($phoneNumber, $message): JsonResponse
    {
        try {
            $response = $this->httpClientService->connectViaGet($this->baseUrl . 'send', [
                'password' => $this->token,
                'username' => $this->username,
                'line' => $this->line,
                'mobile' => $phoneNumber,
                'text' => $message,
            ], [
                'Accept' => 'application/json',
                'X-API-KEY' => $this->token,
            ]);
            if ($response['status'] !== 1) {
                return response()->json([
                    'success' => false,
                    'message' => $response['message']
                ], status: Response::HTTP_BAD_REQUEST);
            }
            return response()->json([
                'success' => true
            ], status: Response::HTTP_OK);

        } catch (Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ], status: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param $phoneNumber
     * @param $pattern
     * @param $parameters
     * @return JsonResponse
     */
    public function sendPatternMessage($phoneNumber, $pattern, $parameters): JsonResponse
    {
        try {
            $response = $this->httpClientService->connectViaPost($this->baseUrl . 'send/verify', [
                'Parameters' => $this->setParameters($parameters),
                'Mobile' => $phoneNumber,
                'TemplateId' => $pattern,
            ], [
                'Accept' => 'application/json',
                'X-API-KEY' => $this->token,
            ]);
            if ($response['status'] !== 1) {
                return response()->json([
                    'success' => false,
                    'message' => $response['message']
                ], status: Response::HTTP_BAD_REQUEST);
            }
            return response()->json([
                'success' => true,
             ], status: Response::HTTP_OK);

        } catch (Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ], status: Response::HTTP_INTERNAL_SERVER_ERROR);
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
            $array[] = ['Name' => $key, 'Value' => $value];
        }
        return $array;
    }
}
