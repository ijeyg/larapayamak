<?php

namespace Ijeyg\Larapayamak\Gateways;

use Ijeyg\Larapayamak\Contracts\AbstractSmsProvider;
use Ijeyg\Larapayamak\Services\HttpClientService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class FarazSms extends AbstractSmsProvider
{
    private mixed $username;
    private mixed $password;
    private mixed $from;
    private string $baseUrl = 'https://ippanel.com/';

    public function __construct($username, $password, $from)
    {
        parent::setHttpClient(new HttpClientService());
        $this->username = $username;
        $this->password = $password;
        $this->from = $from;
    }

    /**
     * @param array|string $phoneNumber
     * @param string $message
     * @return JsonResponse
     */
    public function sendSimpleMessage($phoneNumber, $message): JsonResponse
    {
        try {
            $recipients = is_array($phoneNumber) ? $phoneNumber : [$phoneNumber];

            $response = $this->httpClientService->connectViaPost($this->baseUrl . 'services.jspd', [
                'uname' => $this->username,
                'pass' => $this->password,
                'from' => $this->from,
                'message' => $message,
                'to' => json_encode($recipients),
                'op' => 'send'
            ], [
                'Accept' => 'application/json',
            ]);

            if (!is_array($response) || $response[0] !== 'OK') {
                return response()->json([
                    'success' => false,
                    'message' => $response[1] ?? 'There is an error while processing your request'
                ], status: Response::HTTP_BAD_REQUEST);
            }

            return response()->json([
                'success' => true,
                'message' => $response[1]
            ], status: Response::HTTP_OK);

        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ], status: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param string $phoneNumber
     * @param string $pattern
     * @param array $parameters
     * @return JsonResponse
     */
    public function sendPatternMessage($phoneNumber, $pattern, $parameters): JsonResponse
    {
        try {
            $response = $this->httpClientService->connectViaPost($this->baseUrl . 'patterns/pattern', [
                'username' => $this->username,
                'password' => $this->password,
                'from' => $this->from,
                'to' => [$phoneNumber],
                'input_data' => $parameters,
                'pattern_code' => $pattern,
            ], [
                'Accept' => 'application/json',
            ]);

            if (!isset($response['status']) || $response['status'] !== 'OK') {
                return response()->json([
                    'success' => false,
                    'message' => $response['message'] ?? 'There is an error while processing your request'
                ], status: Response::HTTP_BAD_REQUEST);
            }

            return response()->json([
                'success' => true,
            ], status: Response::HTTP_OK);

        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ], status: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
