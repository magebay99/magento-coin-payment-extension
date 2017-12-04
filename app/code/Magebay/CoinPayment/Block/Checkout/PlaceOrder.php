<?php
/**
 * @Author      Magebay
 * @package     Magebay_CoinPayment
 * @copyright   Copyright (c) 2017 MAGEBAY (https://www.magebay.com)
 * @terms       https://www.magebay.com/terms
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 **/
namespace Magebay\CoinPayment\Block\Checkout;

use \Magento\Framework\View\Element\Template;

class PlaceOrder extends Template
{
    protected $_checkoutSession;
    protected $_customerSession;
    protected $_orderFactory;
    protected $_orderConfig;
    protected $httpContext;
    protected $_mkCoreOrder;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Sales\Model\Order\Config $orderConfig,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Sales\Model\OrderFactory $mkCoreOrder,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_checkoutSession = $checkoutSession;
        $this->_customerSession = $customerSession;
        $this->_orderFactory = $orderFactory;
        $this->_orderConfig = $orderConfig;
        $this->httpContext = $httpContext;
        $this->_mkCoreOrder = $mkCoreOrder;
    }
    
    public function getLastOrderId(){
        return $this->_checkoutSession->getLastOrderId(); 
    }
    
    public function getOrderById($id){
         return $this->_mkCoreOrder->create()->load($id);
    }
    
    public function getCurrentCurrencyCode(){
        return $this->_storeManager->getStore()->getCurrentCurrency()->getCode();
    }
    
    public function getPrice($price){
        return \Magento\Framework\App\ObjectManager::getInstance()->create('Magento\Framework\Pricing\Helper\Data')->currency(number_format($price,2), true, false);
    }
    
    public function getCoinPayments(){
        $coinPayments = \Magento\Framework\App\ObjectManager::getInstance()->create('Magebay\CoinPayment\Model\Payments')->getCollection();
        return $coinPayments;
    }
}