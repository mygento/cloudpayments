<?php
/**
 * @author Mygento Team
 * @copyright Copyright 2017 Mygento (https://www.mygento.ru)
 * @package Mygento_Cloudpayments
 */

namespace Mygento\Cloudpayments\Controller\Payment;

use Mygento\Cloudpayments\Controller\AbstractAction;

class Process extends AbstractAction
{
    /**
     * @return void
     */
    public function execute()
    {
        $orderId = $this->checkoutSession->getLastOrderId();
        if (!$this->helper->isActive() || !$this->_request || !$orderId) {
            $this->_forward('noroute');
            return;
        }

        $this->helper->debug('Pay after order #' . $orderId);
        $order = $this->orderRepository->get($orderId);
        $this->helper->debug($order->canInvoice());
        $this->helper->debug($order->getPayment()->getMethodInstance()->getCode());
        if (!$order->canInvoice() || strpos($order->getPayment()->getMethodInstance()->getCode(), 'cloudpayments') === false) {
            $this->_forward('noroute');
            return;
        }
        $this->_view->loadLayout();
        $this->_view->getLayout()->getBlock('cloudpayments.capture')->setOrder($order)->setSuccess(true);
        $this->_view->renderLayout();
    }
}
