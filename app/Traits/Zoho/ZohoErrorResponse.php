<?php

namespace App\Traits\Zoho;

use App\Services\ZohoService;

trait ZohoErrorResponse
{
    private $code;
    private $message;
    public function __construct($code = null, $message = null)
    {
        $this->code = $code;
        $this->message = $message;
    }
    public function handleErrorResponseCode(){
        switch ($this->code) {
            case 1002:
                return $this->message;
                break;
            case 401:
                return $this->message;
                break;
            case 403:
                return $this->message;
                break;
            case 404:
                return $this->message;
                break;
            case 405:
                return $this->message;
                break;
            case 413:
                return $this->message;
                break;
            case 415:
                return $this->message;
                break;
            case 429:
                return $this->message;
                break;
            case 500:
                return $this->message;
                break;
            case 502:
                return $this->message;
                break;
            case 503:
                return $this->message;
                break;
            case 504:
                return $this->message;
                break;
            default:
                return $this->message;
        }
    }

    public function getContactId($requestData){

        // get contact id from listcontact
        $zohoService = new ZohoService();
        $contact = $zohoService->listContact($requestData);
        return $contact['contacts'][0]['contact_id'] ?? '';
    }


}
