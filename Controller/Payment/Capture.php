<?php

/**
 * @author Mygento Team
 * @copyright 2017-2020 Mygento (https://www.mygento.ru)
 * @package Mygento_Cloudpayments
 */

namespace Mygento\Cloudpayments\Controller\Payment;

use Magento\Framework\View\Result\Page;
use Mygento\Cloudpayments\Controller\AbstractAction;

class Capture extends AbstractAction
{
    /**
     * @return Page|void
     */
    public function execute()
    {
        if (!$this->helper->isActive() || !$this->_request) {
            $this->_forward('noroute');

            return;
        }
        $orderId = $this->helper->decodeId($this->_request->getParam('order'));
        $this->helper->debug('Paynow for order #' . $orderId);
        $order = $this->orderRepository->get($orderId);
        if (!$order->canInvoice() || strpos($order->getPayment()->getMethodInstance()->getCode(), 'cloudpayments') === false) {
            $this->_forward('noroute');

            return;
        }
        $this->_view->loadLayout();
        $this->_view->getLayout()->getBlock('cloudpayments.capture')->setOrder($order);
        $this->_view->renderLayout();
    }
}
