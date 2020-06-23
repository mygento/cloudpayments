<?php

/**
 * @author Mygento Team
 * @copyright 2017-2020 Mygento (https://www.mygento.ru)
 * @package Mygento_Cloudpayments
 */

namespace Mygento\Cloudpayments\Gateway\Http\Client;

use Magento\Payment\Gateway\Http\TransferInterface;

class Cancel extends Client
{
    public function placeRequest(TransferInterface $transferObject)
    {
        $this->helper->debug('void request', ['request' => $transferObject->getBody()]);
        $response = $this->sendRequest('/payments/void', $transferObject->getBody());
        $this->helper->debug('void response', ['response' => $response]);

        return json_decode($response, true);
    }
}
