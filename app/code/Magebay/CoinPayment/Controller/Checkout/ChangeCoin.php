<?php
/**
 * @Author      Magebay
 * @package     Magebay_CoinPayment
 * @copyright   Copyright (c) 2017 MAGEBAY (https://www.magebay.com)
 * @terms       https://www.magebay.com/terms
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 **/
namespace Magebay\CoinPayment\Controller\Checkout;

use Magento\Framework\Controller\ResultFactory;

/**
 * Responsible for loading page content.
 *
 * This is a basic controller that only loads the corresponding layout file. It may duplicate other such
 * controllers, and thus it is considered tech debt. This code duplication will be resolved in future releases.
 */
class ChangeCoin extends \Magento\Framework\App\Action\Action
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
        $order = \Magento\Framework\App\ObjectManager::getInstance()->create('Magento\Sales\Model\Order')->load(\Magento\Framework\App\ObjectManager::getInstance()->create('\Magento\Checkout\Model\Session')->getLastOrderId());
        $data_post =  $this->getRequest()->getParam('data_post');
        $coinPayment = \Magento\Framework\App\ObjectManager::getInstance()->create('Magebay\CoinPayment\Model\Payments')
                                                                          ->getCollection()
                                                                          ->addFieldToFilter('code',$data_post)
                                                                          ->getFirstItem();
        $coinmarketcap = json_decode(file_get_contents('https://api.coinmarketcap.com/v1/ticker/'), true);
        foreach($coinmarketcap as $cmc){
            if($data_post == $cmc['symbol']){
                $data['coin_price_usd'] = $cmc['price_usd'];
                $data['order_price_usd'] = $order->getGrandTotal();
                $data['coin_price_order'] = $order->getGrandTotal()/$cmc['price_usd'];
                $data['coin_price_fee'] = $coinPayment['fee'];
                $data['coin_price_order_total'] = ($order->getGrandTotal()/$cmc['price_usd']) + $coinPayment['fee'];
                $data['coin_name'] = $cmc['name'];
                $data['coin_code'] = $cmc['symbol'];
                $data['coin_address'] = $coinPayment['coin_address'];
                $data['payment-address'] = __('Please send exactly').' '.'<b style="text-decoration: underline;">'.$data['coin_price_order_total'].' '.$data['coin_name'].'</b>'.' '.__('to address below');
                $data['invoice-qr-code'] = '<img src="https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl='.$data['coin_address'].'&choe=UTF-8" title="'.$data['coin_address'].'"/>';
                $data['transaction_fee'] = __('Coin (transaction) fee').' '.$data['coin_price_fee'].' '.__('Coin');
                $data['order-footer-details'] = '1 '.$data['coin_name'].' = '.$data['coin_price_usd'].' USD';
            }
        }
        if($data_post != 'null'){
            $data['status'] = true;
        }else{
            $data['status'] = false;
        }
        $token = $this->generateRandomString();
        \Magento\Framework\App\ObjectManager::getInstance()->create('\Magento\Checkout\Model\Session')->setToken($token);
        $data['token'] = $token;
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($data);
        return $resultJson;
    }
    public function generateRandomString($length = 50) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    public function get_web_page( $url )
    {
        $user_agent = 'Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';
        $options = array(
            CURLOPT_CUSTOMREQUEST  =>"GET",         //set request type post or get
            CURLOPT_POST           =>false,         //set to GET
            CURLOPT_USERAGENT      => $user_agent,  //set user agent
            CURLOPT_COOKIEFILE     =>"cookie.txt",  //set cookie file
            CURLOPT_COOKIEJAR      =>"cookie.txt",  //set cookie jar
            CURLOPT_RETURNTRANSFER => true,         // return web page
            CURLOPT_HEADER         => false,        // don't return headers
            CURLOPT_FOLLOWLOCATION => true,         // follow redirects
            CURLOPT_ENCODING       => "",           // handle all encodings
            CURLOPT_AUTOREFERER    => true,         // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 120,          // timeout on connect
            CURLOPT_TIMEOUT        => 120,          // timeout on response
            CURLOPT_MAXREDIRS      => 10,           // stop after 10 redirects
        );
        $ch      = curl_init( $url );
        curl_setopt_array( $ch, $options );
        $content = curl_exec( $ch );
        $err     = curl_errno( $ch );
        $errmsg  = curl_error( $ch );
        $header  = curl_getinfo( $ch );
        curl_close( $ch );
        $header['errno']   = $err;
        $header['errmsg']  = $errmsg;
        $header['content'] = $content;
        return $header;
    }
    public function getContentFromUrl( $url )
    {
        return file_get_contents( $url );
    }
}