<?php

namespace Ijeyg\Larapayamak\Gateways;

use Ijeyg\Larapayamak\Contracts\AbstractSmsProvider;
use Ijeyg\Larapayamak\Services\HttpClientService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class PayamResan extends AbstractSmsProvider
{
    private string $apiKey;
    private string $baseUrl = 'http://api.sms-webservice.com/api/V3/';

    public function __construct($apiKey)
    {
        parent::setHttpClient(new HttpClientService());
        $this->apiKey = $apiKey;
    }

    public function sendSimpleMessage($phoneNumber, $message): JsonResponse
    {
        try {
            $recipients = is_array($phoneNumber) ? $phoneNumber : [$phoneNumber];

            $response = $this->httpClientService->connectViaPost($this->baseUrl.'SendBulk', [
                'ApiKey' => $this->apiKey,
                'Text' => $message,
                'Sender' => 0,
                'Recipients' => array_map(fn($number) => ['Destination' => $number, 'UserTraceId' => 0], $recipients),
            ], [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ]);

            if (!isset($response['Result'])) {
                return response()->json([
                    'success' => false,
                    'message' => $response ?? 'There is an error while processing your request'
                ], Response::HTTP_BAD_REQUEST);
            }

            return response()->json([
                'success' => true,
            ], Response::HTTP_OK);

        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function sendPatternMessage($phoneNumber, $pattern, $parameters): JsonResponse
    {
        try {
            $queryParams = http_build_query(array_merge([
                'ApiKey' => $this->apiKey,
                'TemplateKey' => $pattern,
                'Destination' => $phoneNumber,
            ], $parameters));

            $response = $this->httpClientService->connectViaGet($this->baseUrl . 'SendTokenSingle?' . $queryParams, [
                'Accept' => 'application/json',
            ]);

            if (!isset($response['Result'])) {
                return response()->json([
                    'success' => false,
                    'message' => $response ?? 'There is an error while processing your request'
                ], Response::HTTP_BAD_REQUEST);
            }

            return response()->json([
                'success' => true,
            ], Response::HTTP_OK);

        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
