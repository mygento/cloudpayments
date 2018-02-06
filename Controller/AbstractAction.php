<?php
/**
 * @author Mygento Team
 * @copyright Copyright 2017 Mygento (https://www.mygento.ru)
 * @package Mygento_Cloudpayments
 */

namespace Mygento\Cloudpayments\Controller;

abstract class AbstractAction extends \Mygento\Payment\Controller\Payment\AbstractAction
{
    /** @var \Mygento\Payment\Helper\Data */
    protected $_resultJsonFactory;

    public function __construct(
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Mygento\Payment\Helper\Data $helper,
        \Mygento\Payment\Helper\Transaction $transHelper,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Framework\App\Action\Context $context
    ) {
        parent::__construct(
            $helper,
            $transHelper,
            $orderFactory,
            $checkoutSession,
            $resultLayoutFactory,
            $context
        );
        $this->_resultJsonFactory = $resultJsonFactory;
    }

    protected function validateOrder($order, $postData)
    {
        if (!isset($postData['Currency']) || $postData['Currency'] !== $order->getOrderCurrencyCode()) {
            $this->_helper->addLog('not valid order currency');
            return false;
        }

        if (!isset($postData['Amount']) || floatval($postData['Amount']) !== round($order->getGrandTotal(), 2)) {
            $this->_helper->addLog('not valid order payment sum');
            return false;
        }
        return true;
    }
}
