<?php
/**
 * @author Mygento Team
 * @copyright Copyright 2017 Mygento (https://www.mygento.ru)
 * @package Mygento_Cloudpayments
 */

namespace Mygento\Cloudpayments\Controller\Callback;

class Refund extends \Mygento\Cloudpayments\Controller\AbstractAction
{
    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        // prevent race condition
        sleep(3);

        $this->_helper->addLog('refund callback');
        $postData = $this->_request->getParams();
        $this->_helper->addLog($postData);
        $signature = $this->_request->getHeader('Content-HMAC');
        $this->_helper->addLog('signature '.$signature);

        $valid = $this->_helper->validateSignature(file_get_contents('php://input'), $signature);
        if (!$valid) {
            $this->_helper->addLog('invalid signature');
            return $this->_resultJsonFactory->create()->setData(['code' => 1]);
        }

        $order = $this->_orderFactory->create()->loadByIncrementId($postData['InvoiceId']);
        if (!$order || !$order->getId()) {
            $this->_helper->addLog('order not found');
            return $this->_resultJsonFactory->create()->setData(['code' => 1]);
        }

        if (!$order->canCreditmemo()) {
            $this->_helper->addLog('order can not be refunded');
            return $this->_resultJsonFactory->create()->setData(['code' => 0]);
        }

        try {
            $this->_transHelper->proceedRefund($order, $postData['TransactionId'], $postData['PaymentTransactionId'], $postData['Amount']);
        } catch (\Exception $e) {
            $this->_helper->addLog($e->getMessage());
            return $this->_resultJsonFactory->create()->setData(['code' => 1]);
        }

        return $this->_resultJsonFactory->create()->setData(['code' => 0]);
    }
}
