<?php

/**
 * @author Mygento Team
 * @copyright 2017-2020 Mygento (https://www.mygento.ru)
 * @package Mygento_Cloudpayments
 */

namespace Mygento\Cloudpayments\Gateway\Config;

/**
 * Class Config
 */
class Config extends \Mygento\Payment\Gateway\Config\Config
{
    /**
     * @return string
     */
    public function getPublicId()
    {
        return trim($this->getValue('public_id'));
    }

    /**
     * @return string|null
     */
    public function getApiKey()
    {
        return $this->encryptor->decrypt($this->getValue('private_key'));
    }
}
