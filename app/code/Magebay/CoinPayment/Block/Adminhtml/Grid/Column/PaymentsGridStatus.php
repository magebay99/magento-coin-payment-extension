<?php
/**
 * @Author      Magebay
 * @package     Magebay_CoinPayment
 * @copyright   Copyright (c) 2017 MAGEBAY (https://www.magebay.com)
 * @terms       https://www.magebay.com/terms
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 **/
namespace Magebay\CoinPayment\Block\Adminhtml\Grid\Column;

use \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;

class PaymentsGridStatus extends AbstractRenderer
{
    public function render(\Magento\Framework\DataObject $row)
    {
        if ($row->getIsActive() || $row->getStatus()) {
            $cell = '<span class="grid-severity-notice"><span>Enabled</span></span>';
        } else {
            $cell = '<span class="grid-severity-critical"><span>Disabled</span></span>';
        }
        return $cell;
    }
}