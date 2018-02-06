<?php
/**
 * @author Mygento Team
 * @copyright Copyright 2017 Mygento (https://www.mygento.ru)
 * @package Mygento_Cloudpayments
 */

namespace Mygento\Cloudpayments\Controller\Payment;

class Process extends \Mygento\Cloudpayments\Controller\AbstractAction
{
    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $orderId = $this->_checkoutSession->getLastOrderId();
        if (!$this->_helper->getConfig('active') || !$this->_request || !$orderId) {
            $this->_forward('noroute');
            return;
        }

        $this->_helper->addLog('Pay after order #' . $orderId);
        $order = $this->_orderFactory->create()->load($orderId);
        $this->_helper->addLog($order->canInvoice());
        $this->_helper->addLog($order->getPayment()->getMethodInstance()->getCode());
        if (!$order->canInvoice() || strpos($order->getPayment()->getMethodInstance()->getCode(), 'cloudpayments') === false) {
            $this->_forward('noroute');
            return;
        }
        $this->_view->loadLayout();
        $this->_view->getLayout()->getBlock('cloudpayments.capture')->setOrder($order)->setSuccess(true);
        $this->_view->renderLayout();
    }
}
