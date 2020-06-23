<?php

namespace Mygento\Cloudpayments\Block\System\Config;

use Magento\Config\Block\System\Config\Form\Fieldset;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Instructions extends Fieldset
{

    /**
     * Return header comment part of html for fieldset
     *
     * @param AbstractElement $element
     * @return string
     */
    protected function _getHeaderCommentHtml($element)
    {
        return '<table class="comment">' .
            '<tr>' .
            '<td colspan="2" style="text-align: center; font-size: 18px;">' .
            __('Settings for Cloudpayments Control Panel') .
            '</td>' .
            '</tr>' .
            '<tr>' .
            '<td>' . __('Check Settings Url') . '</td>' .
            '<td>' . $this->getSiteUrl('cloudpayments/callback/check') . '</td>' .
            '</tr>' .
            '<tr>' .
            '<td>' . __('Pay Settings Url') . '</td>' .
            '<td>' . $this->getSiteUrl('cloudpayments/callback/pay') . '</td>' .
            '</tr>' .
            '<tr>' .
            '<td>' . __('Refund Settings Url') . '</td>' .
            '<td>' . $this->getSiteUrl('cloudpayments/callback/refund') . '</td>' .
            '</tr>' .
            '<tr>' .
            '<td>' . __('Receipt Settings Url') . '</td>' .
            '<td>' . $this->getSiteUrl('cloudpayments/callback/receipt') . '</td>' .
            '</tr>' .
            '</table>';
    }

    public function getSiteUrl($path)
    {
        return $this->_urlBuilder->getBaseUrl(['_secure' => 1]) . $path;
    }
}
