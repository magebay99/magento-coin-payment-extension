<?php
/**
 * @Author      Magebay
 * @package     Magebay_CoinPayment
 * @copyright   Copyright (c) 2017 MAGEBAY (https://www.magebay.com)
 * @terms       https://www.magebay.com/terms
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 **/
namespace Magebay\CoinPayment\Block\Adminhtml;

use Magento\Backend\Block\Widget\Grid\Container;

class Payments extends Container
{
   /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_payments';
        $this->_blockGroup = 'Magebay_CoinPayment';
        $this->_headerText = __('Manage Coin Payments');
        $this->_addButtonLabel = __('Add New Coin Payment');
        parent::_construct();
    }
}