<?php

namespace Ijeyg\Larapayamak\Gateways;

use Ijeyg\Larapayamak\Contracts\AbstractSmsProvider;
use Ijeyg\Larapayamak\Services\HttpClientService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class Farapayamak extends AbstractSmsProvider
{
    private mixed $username;
    private mixed $line;
    private mixed $password;

    private string $baseUrl = 'https://rest.payamak-panel.com/api/SendSMS/';

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
            $response = $this->httpClientService->connectViaPost($this->baseUrl . 'SendSMS', [
                'password' => $this->password,
                'username' => $this->username,
                'from' => $this->line,
                'to' => $phoneNumber,
                'text' => $message,
                'isflash' => 'false'
            ], [
                'Accept' => 'application/json',
            ]);
            if ($response['RetStatus'] !== 1) {
                return response()->json([
                    'success' => false,
                    'message' => $response['StrRetStatus']
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
        try {
            $response = $this->httpClientService->connectViaPost($this->baseUrl . 'BaseServiceNumber', [
                'username' => $this->username,
                'password' => $this->password,
                'to' => $phoneNumber,
                'bodyId' => $pattern,
                'text' => $this->setParameters($parameters)
            ], [
                'Accept' => 'application/json',
            ]);
            if ($response['RetStatus'] !== 1) {
                return response()->json([
                    'success' => false,
                    'message' => $response['StrRetStatus']
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

    /**
     * @param array $parameters
     * @return string
     */
    private function setParameters(array $parameters): string
    {
        $array = null;
        $count = count($parameters);
        $index = 0;
        foreach ($parameters as $key => $value) {
            $index++;
            if ($index === $count) {
                $array .= $value;
            }else{
                $array .= $value . ';';
            }
        }
        return $array;
    }
}
