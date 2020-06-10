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

class Check extends AbstractAction implements CsrfAwareActionInterface
{

    /**
     * @return Json
     */
    public function execute()
    {
        $postData = $this->_request->getParams();
        $this->helper->debug('check callback: ', $postData);
        $signature = $this->_request->getHeader('Content-HMAC');
        $this->helper->debug('signature ' . $signature);

        $valid = $this->helper->validateSignature(file_get_contents('php://input'), $signature);
        if (!$valid) {
            $this->helper->debug('invalid signature');
            return $this->resultJsonFactory->create()->setData(['code' => 1]);
        }

        if (!isset($postData['Status']) || !in_array($postData['Status'], ['Completed', 'Authorized'])) {
            $this->helper->debug('not paid status');
            return $this->resultJsonFactory->create()->setData(['code' => 0]);
        }

        $order = $this->orderFactory->create()->loadByIncrementId($postData['InvoiceId']);
        if (!$order || !$order->getId()) {
            $this->helper->debug('order not found');
            return $this->resultJsonFactory->create()->setData(['code' => 1]);
        }

        if (!$order->canInvoice()) {
            $this->helper->debug('order can not be invoiced');
            return $this->resultJsonFactory->create()->setData(['code' => 1]);
        }

        $valid = $this->validateOrder($order, $postData);
        if (!$valid) {
            $this->helper->debug('not valid order data');
            return $this->resultJsonFactory->create()->setData(['code' => 1]);
        }
        $this->helper->debug('allow to process');

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
