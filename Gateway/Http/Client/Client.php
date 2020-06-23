<?php

/**
 * @author Mygento Team
 * @copyright 2017-2020 Mygento (https://www.mygento.ru)
 * @package Mygento_Cloudpayments
 */

namespace Mygento\Cloudpayments\Gateway\Http\Client;

class Client extends \Mygento\Payment\Gateway\Http\Client\Client
{
    /**
     * @var string
     */
    protected $url = 'https://api.cloudpayments.ru';

    /**
     * @param string $path
     * @param array $params
     * @return string
     */
    protected function sendRequest($path, array $params = [])
    {
        $this->curl->setOption(
            CURLOPT_USERPWD,
            $this->config->getPublicId() . ':' . $this->config->getApiKey()
        );
        $this->curl->post($this->url . $path, $params);

        return $this->curl->getBody();
    }
}
