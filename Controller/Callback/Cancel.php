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

class Cancel extends AbstractAction implements CsrfAwareActionInterface
{
    /**
     * @return Json
     */
    public function execute()
    {
        // prevent race condition
        sleep(3);

        $postData = $this->_request->getParams();
        $this->helper->debug('void callback: ', $postData);
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

        if (!$order->canVoidPayment()) {
            $this->helper->error('order can not be void');
            return $this->resultJsonFactory->create()->setData(['code' => 0]);
        }

        try {
            $this->transHelper->proceedVoid($order, $postData['TransactionId'], $postData['PaymentTransactionId'], $postData['Amount']);
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
