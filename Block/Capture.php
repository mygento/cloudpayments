<?php

/**
 * @author Mygento Team
 * @copyright 2017-2020 Mygento (https://www.mygento.ru)
 * @package Mygento_Cloudpayments
 */

namespace Mygento\Cloudpayments\Block;

use Magento\Sales\Model\Order;

class Capture extends AbstractInfo
{
    /**
     * @var string
     */
    protected $_template = 'Mygento_Cloudpayments::form/capture.phtml';

    /**
     * @var Order
     */
    protected $_order;

    protected $_success;

    /**
     * @param Order $order
     * @return $this
     */
    public function setOrder($order)
    {
        $this->_order = $order;

        return $this;
    }

    public function getOrder()
    {
        return $this->_order;
    }

    /**
     * @param bool $flag
     * @return $this
     */
    public function setSuccess($flag)
    {
        $this->_success = $flag;

        return $this;
    }

    public function getSuccess()
    {
        return $this->_success;
    }

    /**
     * @return bool
     */
    public function isTwoStep()
    {
        return $this->getConfig()->getValue('step') == 2;
    }

    public function getSuccessPage()
    {
        return $this->getConfig()->getValue('success_page') ?: null;
    }

    public function getErrorPage()
    {
        return $this->getConfig()->getValue('error_page') ?: null;
    }

    /**
     * @return array
     */
    public function getWidgetData()
    {
        $order = $this->getOrder();
        $result = [
            'publicId' => $this->getConfig()->getPublicId(),
            'description' => $this->getConfig()->getValue('order_desc'),
            'amount' => round($order->getGrandTotal(), 2),
            'currency' => $order->getOrderCurrencyCode(),
            'invoiceId' => $order->getIncrementId(),
            'accountId' => $order->getCustomerEmail(),
        ];
        if ($this->getConfig()->getValue('tax')) {
            $shippingTax = $this->getConfig()->getValue('tax_shipping');
            $taxValue = $this->getConfig()->getValue('tax_options');

            $attributeCode = '';
            if (!$this->getConfig()->getValue('tax_all')) {
                $attributeCode = $this->getConfig()->getValue('product_tax_attr');
            }

            if (!$this->getConfig()->getValue('default_shipping_name')) {
                $order->setShippingDescription(
                    $this->getConfig()->getValue('custom_shipping_name')
                );
            }

            $data = $this->getTaxHelper()->getRecalculated(
                $order,
                $taxValue,
                $attributeCode,
                $shippingTax
            );
            $taxResult = ['Items' => []];
            foreach ($data['items'] as $item) {
                $value = filter_var($item['tax'], FILTER_SANITIZE_NUMBER_INT);
                $taxResult['Items'][] = [
                    'label' => $item['name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'amount' => $item['sum'],
                    'vat' => empty($value) ? null : abs($value),
                ];
            }
            $result['data'] = [
                'cloudPayments' => [
                    'customerReceipt' => $taxResult,
                ],
            ];
        }

        return json_encode($result);
    }
}
