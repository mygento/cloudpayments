<?php

namespace Mygento\Cloudpayments\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Tax implements OptionSourceInterface
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
            ['value' => 18, 'label' => __('VAT18')],
            ['value' => 10, 'label' => __('VAT10')],
            ['value' => 0, 'label' => __('VAT0')],
            ['value' => 110, 'label' => __('VAT110')],
            ['value' => 118, 'label' => __('VAT118')],
            ['value' => 120, 'label' => __('VAT120')],
            ['value' => null, 'label' => __('VAT Free')],
        ];
    }
}
