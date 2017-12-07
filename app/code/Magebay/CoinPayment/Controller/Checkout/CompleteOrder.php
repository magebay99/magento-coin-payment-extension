<?php
/**
 * @Author      Magebay
 * @package     Magebay_CoinPayment
 * @copyright   Copyright (c) 2017 MAGEBAY (https://www.magebay.com)
 * @terms       https://www.magebay.com/terms
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 **/
namespace Magebay\CoinPayment\Controller\Checkout;

use Magento\Sales\Model\Order;
use Magento\Framework\Controller\ResultFactory;

/**
 * Responsible for loading page content.
 *
 * This is a basic controller that only loads the corresponding layout file. It may duplicate other such
 * controllers, and thus it is considered tech debt. This code duplication will be resolved in future releases.
 */
class CompleteOrder extends \Magento\Framework\App\Action\Action
{
    /** @var \Magento\Framework\View\Result\PageFactory  */
    protected $resultPageFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }
    public function execute()
    {   
        $data = $this->getRequest()->getParams();
        if($this->getRequest()->getParam('token') == \Magento\Framework\App\ObjectManager::getInstance()->create('\Magento\Checkout\Model\Session')->getToken()){
            $order = \Magento\Framework\App\ObjectManager::getInstance()->create('Magento\Sales\Model\Order')->load(\Magento\Framework\App\ObjectManager::getInstance()->create('\Magento\Checkout\Model\Session')->getLastOrderId());
            //$order->setState(Order::STATE_PROCESSING)->setStatus(Order::STATE_PROCESSING);
            $comment  = __('Coin Address Sendor : ').$data['coin_address_sendor'].'<br>';
            $comment .= __('Coin Code : ').$data['coin_code'].'<br>';
            $comment .= __('Order Coin Price : ').$data['coin_price'].' '.$data['coin_code'].'<br>';
            $comment .= __('Coin Address Recieve : ').$data['coin_address_recieve'].'<br>';
            $order->addStatusHistoryComment($comment)->setIsCustomerNotified(false)->setEntityName('order');
            $order->save();
            $this->_redirect('checkout/onepage/success');
        }
    }
}