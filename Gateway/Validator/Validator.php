<?php

/**
 * @author Mygento Team
 * @copyright Copyright 2017 Mygento (https://www.mygento.ru)
 * @package Mygento_Cloudpayments
 */

namespace Mygento\Cloudpayments\Gateway\Validator;

class Validator extends \Mygento\Payment\Gateway\Validator\Validator
{
    /**
     * @inheritdoc
     */
    public function validate(array $validationSubject)
    {
        $response = $validationSubject['response'];
        $isValid = (isset($response['Success']) && $response['Success'] === true);
        $errorMessages = [];
        if (!$isValid && isset($response['Message'])) {
            array_push($errorMessages, $response['Message']);
        }

        $this->helper->debug('Validate result: ' . $isValid);

        return $this->createResult($isValid, $errorMessages);
    }
}
