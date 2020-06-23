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
     * @param ActionContext $context
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    public function __construct(
        JsonFactory $resultJsonFactory,
        ActionContext $context
    ) {
        parent::__construct($context->context);
        $this->orderRepository = $context->orderRepository;
        $this->resultJsonFactory = $resultJsonFactory;
    }

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
