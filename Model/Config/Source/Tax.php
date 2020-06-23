<?php

/**
 * @author Mygento Team
 * @copyright 2017-2020 Mygento (https://www.mygento.ru)
 * @package Mygento_Cloudpayments
 */

namespace Mygento\Cloudpayments\Model\Config\Source;

class Tax implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return [
            ['value' => 20, 'label' => __('VAT20')],
            ['value' => 10, 'label' => __('VAT10')],
            ['value' => 0, 'label' => __('VAT0')],
            ['value' => 110, 'label' => __('VAT110')],
            ['value' => 120, 'label' => __('VAT120')],
            ['value' => null, 'label' => __('VAT Free')],
        ];
    }
}
