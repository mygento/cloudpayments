<?php

/**
 * @author Mygento Team
 * @copyright 2017-2020 Mygento (https://www.mygento.ru)
 * @package Mygento_Cloudpayments
 */

namespace Mygento\Cloudpayments\Gateway\Http\Client;

use Magento\Payment\Gateway\Http\TransferInterface;

class Refund extends Client
{
    public function placeRequest(TransferInterface $transferObject)
    {
        $this->helper->debug('Refund request', ['request' => $transferObject->getBody()]);
        $response = $this->sendRequest('/payments/refund', $transferObject->getBody());
        $this->helper->debug('Refund response', ['response' => $response]);

        return json_decode($response, true);
    }
}
