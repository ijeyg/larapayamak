<?php

namespace Ijeyg\Larapayamak\Gateways;

use Ijeyg\Larapayamak\Contracts\AbstractSmsProvider;
use Ijeyg\Larapayamak\Services\HttpClientService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class NikSms extends AbstractSmsProvider
{
    private mixed $username;
    private mixed $line;
    private mixed $password;

    private string $baseUrl = 'http://niksms.com/fa/PublicApi/';

    public function __construct($username, $line, $password)
    {
        parent::setHttpClient(new HttpClientService());
        $this->username = $username;
        $this->line = $line;
        $this->password = $password;
    }

    public function sendSimpleMessage($phoneNumber, $message): JsonResponse
    {
        try {
            $response = $this->httpClientService->connectViaPost($this->baseUrl . 'GroupSms', [
                'username' => $this->username,
                'password' => $this->password,
                'message' => $message,
                'numbers' => $phoneNumber,
                'senderNumber' => $this->line,
                'sendOn' => null,
                'yourMessageIds' => null,
                'sendType' => 1,
            ], [
                'Accept' => 'application/json',
            ]);
            if ($response['status'] !== 'ok') {
                return response()->json([
                    'success' => false,
                    'message' => $response['error']
                ], status: Response::HTTP_BAD_REQUEST);
            }
            return response()->json([
                'success' => true
            ], status: Response::HTTP_OK);

        } catch (\Exception $exception) {
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
        return response()->json([
            'success' => false,
            'message' => "This Method is not implemented yet."
        ], status: Response::HTTP_BAD_REQUEST);

    }
}
