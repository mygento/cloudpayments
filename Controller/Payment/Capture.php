<?php
/**
 * @author Mygento Team
 * @copyright Copyright 2017 Mygento (https://www.mygento.ru)
 * @package Mygento_Cloudpayments
 */

namespace Mygento\Cloudpayments\Controller\Payment;

class Capture extends \Mygento\Cloudpayments\Controller\AbstractAction
{
    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if (!$this->_helper->getConfig('active') || !$this->_request) {
            $this->_forward('noroute');
            return;
        }
        $orderId = $this->_helper->decodeId($this->_request->getParam('order'));
        $this->_helper->addLog('Paynow for order #' . $orderId);
        $order = $this->_orderFactory->create()->load($orderId);
        if (!$order->canInvoice() || strpos($order->getPayment()->getMethodInstance()->getCode(), 'cloudpayments') === false) {
            $this->_forward('noroute');
            return;
        }
        $this->_view->loadLayout();
        $this->_view->getLayout()->getBlock('cloudpayments.capture')->setOrder($order);
        $this->_view->renderLayout();
    }
}
