<?php
/**
 * @author Mygento Team
 * @copyright Copyright 2017 Mygento (https://www.mygento.ru)
 * @package Mygento_Cloudpayments
 */

namespace Mygento\Cloudpayments\Controller\Payment;

use Magento\Framework\Controller\ResultFactory;

class Redirect extends Process
{
    /**
     * @return void
     */
    public function execute()
    {
        if (!$this->helper->isActive() || !$this->_request) {
            $this->_forward('noroute');
            return;
        }
        $hashKey = $this->getRequest()->getParam('order');
        $orderId = $this->helper->decodeLink($hashKey);
        $this->helper->debug('Paynow for order #' . $orderId);
        $order = $this->orderRepository->get($orderId);
        if (!$order->canInvoice() || strpos($order->getPayment()->getMethodInstance()->getCode(), 'cloudpayments') === false) {
            $this->_forward('noroute');
            return;
        }
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('cloudpayments/payment/process', $this->_request->getParams());
    }
}
