<?php
/**
 * @author Mygento Team
 * @copyright Copyright 2017 Mygento (https://www.mygento.ru)
 * @package Mygento_Cloudpayments
 */

namespace Mygento\Cloudpayments\Controller\Callback;

use Exception;
use Magento\Framework\Controller\Result\Json;
use Mygento\Cloudpayments\Controller\AbstractAction;

class Pay extends AbstractAction
{
    /**
     * @return Json
     */
    public function execute()
    {
        $this->helper->debug('pay callback');
        $postData = $this->_request->getParams();
        $this->helper->debug(json_encode($postData));
        $signature = $this->_request->getHeader('Content-HMAC');
        $this->helper->debug('signature ' . $signature);

        $valid = $this->helper->validateSignature(file_get_contents('php://input'), $signature);
        if (!$valid) {
            $this->helper->error('invalid signature');
            return $this->resultJsonFactory->create()->setData(['code' => 1]);
        }

        if (!isset($postData['Status']) || !in_array($postData['Status'], ['Completed', 'Authorized'])) {
            $this->helper->error('not paid status');
            return $this->resultJsonFactory->create()->setData(['code' => 0]);
        }

        $order = $this->orderFactory->create()->loadByIncrementId($postData['InvoiceId']);
        if (!$order || !$order->getId()) {
            $this->helper->error('order not found');
            return $this->resultJsonFactory->create()->setData(['code' => 1]);
        }

        if (!$order->canInvoice()) {
            $this->helper->error('order can not be invoiced');
            return $this->resultJsonFactory->create()->setData(['code' => 0]);
        }

        $valid = $this->validateOrder($order, $postData);
        if (!$valid) {
            $this->helper->error('not valid order data');
            return $this->resultJsonFactory->create()->setData(['code' => 1]);
        }

        unset($postData['Token']);
        unset($postData['Data']);

        try {
            if ($postData['Status'] == 'Authorized') {
                $this->transHelper->proceedAuthorize($order, $postData['TransactionId'], $postData['Amount'], $postData);
                $this->helper->debug('order authorized');
            }

            if ($postData['Status'] == 'Completed') {
                $this->transHelper->proceedCapture($order, $postData['TransactionId'], $postData['Amount'], $postData);
                $this->helper->debug('order invoiced');
            }
        } catch (Exception $e) {
            $this->helper->warning($e->getMessage());
            return $this->resultJsonFactory->create()->setData(['code' => 1]);
        }

        $this->helper->debug('pay success response');
        return $this->resultJsonFactory->create()->setData(['code' => 0]);
    }
}
