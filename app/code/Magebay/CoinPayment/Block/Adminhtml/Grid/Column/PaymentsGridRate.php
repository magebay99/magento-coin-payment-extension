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

class PaymentsGridRate extends AbstractRenderer
{
    protected $_objectmanager;
    
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectmanager
    ) {
        $this->_objectmanager = $objectmanager;
    }
    
    public function render(\Magento\Framework\DataObject $row)
    {
        if($row->getAutoRate() == 1){
            $data = json_decode(file_get_contents('https://api.coinmarketcap.com/v1/ticker/'), true);
            foreach($data as $dt){
                if($dt['symbol'] == $row->getCode()){
                    $cell = '<div class="data-grid-cell-content">'.$this->_objectmanager->create('Magento\Framework\Pricing\Helper\Data')->currency($dt['price_usd'], true, false).'</div>';
                }
            }
        }else{
            $cell = '<div class="data-grid-cell-content">'.$this->_objectmanager->create('Magento\Framework\Pricing\Helper\Data')->currency($row->getRate(), true, false).'</div>';
        }
        return $cell;
    }
}