<?php

namespace Mygento\Cloudpayments\Model\Config\Source;

class Tax implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return [
            ['value' => 18, 'label' => __('VAT18')],
            ['value' => 10, 'label' => __('VAT10')],
            ['value' => 0, 'label' => __('VAT0')],
            ['value' => 110, 'label' => __('VAT110')],
            ['value' => 118, 'label' => __('VAT118')],
            ['value' => null, 'label' => __('VAT Free')],
        ];
    }
}
