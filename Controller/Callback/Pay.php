<?php
/**
 * @author Mygento Team
 * @copyright Copyright 2017 Mygento (https://www.mygento.ru)
 * @package Mygento_Cloudpayments
 */

namespace Mygento\Cloudpayments\Controller\Callback;

class Pay extends \Mygento\Cloudpayments\Controller\AbstractAction
{
    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $this->_helper->addLog('pay callback');
        $postData = $this->_request->getParams();
        $this->_helper->addLog($postData);
        $signature = $this->_request->getHeader('Content-HMAC');
        $this->_helper->addLog('signature '.$signature);

        $valid = $this->_helper->validateSignature(file_get_contents('php://input'), $signature);
        if (!$valid) {
            $this->_helper->addLog('invalid signature');
            return $this->_resultJsonFactory->create()->setData(['code' => 1]);
        }

        if (!isset($postData['Status']) || !in_array($postData['Status'], ['Completed', 'Authorized'])) {
            $this->_helper->addLog('not paid status');
            return $this->_resultJsonFactory->create()->setData(['code' => 0]);
        }

        $order = $this->_orderFactory->create()->loadByIncrementId($postData['InvoiceId']);
        if (!$order || !$order->getId()) {
            $this->_helper->addLog('order not found');
            return $this->_resultJsonFactory->create()->setData(['code' => 1]);
        }

        if (!$order->canInvoice()) {
            $this->_helper->addLog('order can not be invoiced');
            return $this->_resultJsonFactory->create()->setData(['code' => 0]);
        }

        $valid = $this->validateOrder($order, $postData);
        if (!$valid) {
            $this->_helper->addLog('not valid order data');
            return $this->_resultJsonFactory->create()->setData(['code' => 1]);
        }

        unset($postData['Token']);
        unset($postData['Data']);
        
        try {
            if ($postData['Status'] == 'Authorized') {
                $this->_transHelper->proceedAuthorize($order, $postData['TransactionId'], $postData['Amount'], $postData);
                $this->_helper->addLog('order authorized');
            }

            if ($postData['Status'] == 'Completed') {
                $this->_transHelper->proceedCapture($order, $postData['TransactionId'], $postData['Amount'], $postData);
                $this->_helper->addLog('order invoiced');
            }
        } catch (\Exception $e) {
            $this->_helper->addLog($e->getMessage());
            return $this->_resultJsonFactory->create()->setData(['code' => 1]);
        }

        $this->_helper->addLog('pay success response');
        return $this->_resultJsonFactory->create()->setData(['code' => 0]);
    }
}
