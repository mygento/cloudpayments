<?php
/**
 * @author Mygento Team
 * @copyright Copyright 2017 Mygento (https://www.mygento.ru)
 * @package Mygento_Cloudpayments
 */

namespace Mygento\Cloudpayments\Controller\Callback;

class Receipt extends \Mygento\Cloudpayments\Controller\AbstractAction
{
    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $this->_helper->addLog('receipt callback');
        $postData = $this->_request->getParams();

        $this->_helper->addLog($postData);
        $signature = $this->_request->getHeader('Content-HMAC');
        $this->_helper->addLog('signature ' . $signature);

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

        $postData['Receipt'] = json_decode($postData['Receipt'], true);
        $postData['ReceiptEmail'] = $postData['Receipt']['Email'];
        $postData['ReceiptPhone'] = $postData['Receipt']['Phone'];

        $i = 1;
        foreach ($postData['Receipt']['Items'] as $item) {
            $postData['ReceiptItem'.$i] = json_encode($item);
        }
        unset($postData['Receipt']);

        try {
            if ($postData['Type'] == 'Income') {
                $this->_transHelper->proceedReceipt($order, $postData['Id'], $postData['TransactionId'], $postData);
                $this->_helper->addLog('receipt income');
            }

            if ($postData['Type'] == 'IncomeReturn') {
                $this->_transHelper->proceedRefundReceipt($order, $postData['Id'], $postData['TransactionId'], $postData);
                $this->_helper->addLog('receipt refund');
            }
        } catch (\Exception $e) {
            $this->_helper->addLog($e->getMessage());
            return $this->_resultJsonFactory->create()->setData(['code' => 1]);
        }

        return $this->_resultJsonFactory->create()->setData(['code' => 0]);
    }
}
