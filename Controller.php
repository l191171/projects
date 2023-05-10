<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use SoapClient;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


      public function sendSMS($phone,$message)
    {

     $client = new SoapClient('http://www.smsgateway.ca/sendsms.asmx?WSDL');
        $parameters = new requests;

        $parameters->CellNumber = $phone;
        $parameters->AccountKey = '43rW10So727gw8E58mRL25Glmhp2CPzY';
        $parameters->MessageBody = $message;

        $Result = $client->SendMessage($parameters);

    } 

        

}

