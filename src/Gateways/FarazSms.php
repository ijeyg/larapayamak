<?php

namespace Ijeyg\Larapayamak\Gateways;

use Ijeyg\Larapayamak\Contracts\AbstractSmsProvider;
use Ijeyg\Larapayamak\Contracts\SmsProviderInterface;

class FarazSms {
// extends AbstractSmsProvider implements SmsProviderInterface
//{
//    private mixed $username;
//    private mixed $password;
//    private mixed $line;
//
//    private string $sendSimpleMessageUrl = "https://ippanel.com/services.jspd";
//
//    protected function __construct($username, $password, $line)
//    {
//        parent::__construct();
//        $this->username = $username;
//        $this->password = $password;
//        $this->line = $line;
//    }
//
//    public function sendSimpleMessage($phoneNumber, $message)
//    {
//        try {
//            return $this->httpClientService->connectViaGet($this->sendSimpleMessageUrl . 'send', [
//                'uname' => $this->username,
//                'pass' => $this->password,
//                'from' => $this->line,
//                'message' => $message,
//                'to' => json_encode([$phoneNumber]),
//                'op' => 'send'
//            ]);
//        } catch (\Exception $exception) {
//            throw new \Exception($exception->getMessage(), $exception->getCode(), $exception);
//        }
//    }
//
//    public function sendPatternMessage($phoneNumber, $pattern, $parameters)
//    {
//        $client = new \SoapClient("http://ippanel.com/class/sms/wsdlservice/server.php?wsdl");
//        $user = setting('farazsms_user');
//        $pass = setting('farazsms_pass');
//        $fromNum = setting('farazsms_line_number');
//
//
//        $toNum = array($notifiable->mobile);
//        $pattern_code = $notification->pattern;
//        $input_data = $notification->input_data;
//        $client->sendPatternSms($fromNum, $toNum, $user, $pass, $pattern_code, $input_data);
//
//    }
}
