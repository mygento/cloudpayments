<?php

/**
 * @author Mygento Team
 * @copyright 2017-2020 Mygento (https://www.mygento.ru)
 * @package Mygento_Cloudpayments
 */

namespace Mygento\Cloudpayments\Helper;

/**
 * Cloudpayments Data helper
 */
class Data extends \Mygento\Payment\Helper\Data
{
    const CODE = 'cloudpayments';

    /**
     * @var string
     */
    protected $code = self::CODE;

    /**
     * @param string $message
     * @param string $signature
     * @return bool
     */
    public function validateSignature($message, $signature)
    {
        $s = hash_hmac('sha256', $message, $this->decrypt($this->getConfig('private_key')), true);

        return base64_encode($s) === $signature;
    }
}
