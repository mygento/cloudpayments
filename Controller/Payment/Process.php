<?php

/**
 * @author Mygento Team
 * @copyright 2017-2020 Mygento (https://www.mygento.ru)
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
        if ($hkey = $this->_request->getParam('order')) {
            $orderId = $this->helper->decodeLink($hkey);
        } else {
            $orderId = $this->checkoutSession->getLastOrderId();
        }
        if (!$this->helper->isActive() || !$this->_request || !$orderId) {
            $this->_forward('noroute');

            return;
        }

        $this->helper->debug('Pay after order #' . $orderId);
        $order = $this->orderRepository->get($orderId);
        $code = $order->getPayment()->getMethodInstance()->getCode();
        $this->helper->debug('Pay process controller', [
            'can_invoice' => $order->canInvoice(),
            'code' => $code,
        ]);
        if (!$order->canInvoice() || strpos($code, 'cloudpayments') === false) {
            $this->_forward('noroute');

            return;
        }
        $this->_view->loadLayout();
        $this->_view->getLayout()->getBlock('cloudpayments.capture')->setOrder($order)->setSuccess(true);
        $this->_view->renderLayout();
    }
}
