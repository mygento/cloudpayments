<?php
/**
 * @author Mygento Team
 * @copyright Copyright 2017 Mygento (https://www.mygento.ru)
 * @package Mygento_Cloudpayments
 */

namespace Mygento\Cloudpayments\Helper;

/**
 * Cloudpayments Data helper
 */
class Data extends \Mygento\Payment\Helper\Data
{

    protected $_code = 'cloudpayments';

    /**
     * @param string $message
     * @return boolean
     */
    public function validateSignature($message, $signature)
    {
        $s = hash_hmac('sha256', $message, $this->decrypt($this->getConfig('private_key')), true);
        return base64_encode($s) === $signature;
    }
}
