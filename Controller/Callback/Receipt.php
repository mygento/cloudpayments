<?php
/**
 * @author Mygento Team
 * @copyright Copyright 2017 Mygento (https://www.mygento.ru)
 * @package Mygento_Cloudpayments
 */

namespace Mygento\Cloudpayments\Controller\Callback;

use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Json;
use Mygento\Cloudpayments\Controller\AbstractAction;

class Receipt extends AbstractAction implements CsrfAwareActionInterface
{
    /**
     * @return Json
     */
    public function execute()
    {
        $postData = $this->_request->getParams();

        $this->helper->debug('receipt callback', $postData);
        $signature = $this->_request->getHeader('Content-HMAC');
        $this->helper->debug('signature ' . $signature);

        $valid = $this->helper->validateSignature(file_get_contents('php://input'), $signature);
        if (!$valid) {
            $this->helper->error('invalid signature');
            return $this->resultJsonFactory->create()->setData(['code' => 1]);
        }

        $order = $this->orderFactory->create()->loadByIncrementId($postData['InvoiceId']);
        if (!$order || !$order->getId()) {
            $this->helper->error('order not found');
            return $this->resultJsonFactory->create()->setData(['code' => 1]);
        }

        $postData['Receipt'] = json_decode($postData['Receipt'], true);
        $postData['ReceiptEmail'] = $postData['Receipt']['Email'];
        $postData['ReceiptPhone'] = $postData['Receipt']['Phone'];

        $i = 1;
        foreach ($postData['Receipt']['Items'] as $item) {
            $postData['ReceiptItem' . $i] = json_encode($item);
        }
        unset($postData['Receipt']);

        try {
            if ($postData['Type'] == 'Income') {
                $this->transHelper->proceedReceipt($order, $postData['Id'], $postData['TransactionId'], $postData);
                $this->helper->debug('receipt income');
            }

            if ($postData['Type'] == 'IncomeReturn') {
                $this->transHelper->proceedRefundReceipt($order, $postData['Id'], $postData['TransactionId'], $postData);
                $this->helper->debug('receipt refund');
            }
        } catch (\Exception $e) {
            $this->helper->warning($e->getMessage());
            return $this->resultJsonFactory->create()->setData(['code' => 1]);
        }

        return $this->resultJsonFactory->create()->setData(['code' => 0]);
    }

    /**
     * {@inheritDoc}
     */
    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }
}
