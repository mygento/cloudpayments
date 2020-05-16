<?php

/**
 * @author Mygento Team
 * @copyright Copyright 2017 Mygento (https://www.mygento.ru)
 * @package Mygento_Cloudpayments
 */

namespace Mygento\Cloudpayments\Gateway\Config;

/**
 * Class Config
 */
class Config extends \Mygento\Payment\Gateway\Config\Config
{

    public function getPublicId()
    {
        return trim($this->getValue('public_id'));
    }

    public function getApiKey()
    {
        return $this->encryptor->decrypt($this->getValue('private_key'));
    }
}
