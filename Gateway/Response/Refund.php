<?php

/**
 * @author Mygento Team
 * @copyright Copyright 2017 Mygento (https://www.mygento.ru)
 * @package Mygento_Cloudpayments
 */

namespace Mygento\Cloudpayments\Gateway\Response;

use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Helper\SubjectReader;

class Refund extends Response
{

    /**
     * Handles response
     *
     * @param array $handlingSubject
     * @param array $response
     * @return void
     */
    public function handle(array $handlingSubject, array $response)
    {
        $payment = SubjectReader::readPayment($handlingSubject);

        $payment = $payment->getPayment();

        /**
         * close current transaction because you have refunded the goods
         * but only on full refund close the authorisation
         */
        $payment->setIsTransactionClosed(true);
        $closeParent = !(bool)$payment->getCreditmemo()->getInvoice()->canRefund();
        $payment->setShouldCloseParentTransaction($closeParent);
    }
}
