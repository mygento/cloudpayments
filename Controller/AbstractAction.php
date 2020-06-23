<?php

/**
 * @author Mygento Team
 * @copyright 2017-2020 Mygento (https://www.mygento.ru)
 * @package Mygento_Cloudpayments
 */

namespace Mygento\Cloudpayments\Controller;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Sales\Api\OrderRepositoryInterface;
use Mygento\Cloudpayments\Helper\Data;

abstract class AbstractAction extends \Mygento\Payment\Controller\Payment\AbstractAction
{
    /**
     * @var string
     */
    protected $code = Data::CODE;

    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @param JsonFactory $resultJsonFactory
     * @param ActionContext $context
     */
    public function __construct(
        JsonFactory $resultJsonFactory,
        ActionContext $context
    ) {
        parent::__construct($context->context);
        $this->orderRepository = $context->orderRepository;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @param array $postData
     * @return bool
     */
    protected function validateOrder($order, $postData)
    {
        if (!isset($postData['Currency']) || $postData['Currency'] !== $order->getOrderCurrencyCode()) {
            $this->helper->error('not valid order currency');

            return false;
        }

        if (!isset($postData['Amount']) || floatval($postData['Amount']) !== round($order->getGrandTotal(), 2)) {
            $this->helper->error('not valid order payment sum');

            return false;
        }

        return true;
    }
}
