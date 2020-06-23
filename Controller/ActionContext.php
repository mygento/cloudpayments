<?php

/**
 * @author Mygento Team
 * @copyright 2017-2020 Mygento (https://www.mygento.ru)
 * @package Mygento_Cloudpayments
 */

namespace Mygento\Cloudpayments\Controller;

use Magento\Sales\Api\OrderRepositoryInterface;
use Mygento\Cloudpayments\Helper\Data as MethodsHelper;

class ActionContext
{
    /**
     * @var MethodsHelper
     */
    public $paymentConfigHelper;

    /**
     * @var OrderRepositoryInterface
     */
    public $orderRepository;

    /**
     * @var \Mygento\Payment\Controller\Payment\ActionContext
     */
    public $context;

    /**
     * @param MethodsHelper $paymentConfigHelper
     * @param OrderRepositoryInterface $orderRepository
     * @param \Mygento\Payment\Controller\Payment\ActionContext $context
     */
    public function __construct(
        MethodsHelper $paymentConfigHelper,
        OrderRepositoryInterface $orderRepository,
        \Mygento\Payment\Controller\Payment\ActionContext $context
    ) {
        $this->paymentConfigHelper = $paymentConfigHelper;
        $this->orderRepository = $orderRepository;
        $this->context = $context;
    }
}
