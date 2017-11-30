<?php
/**
 * @Author      Magebay
 * @package     Magebay_CoinPayment
 * @copyright   Copyright (c) 2017 MAGEBAY (https://www.magebay.com)
 * @terms       https://www.magebay.com/terms
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 **/
namespace Magebay\CoinPayment\Helper;
 
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Catalog\Api\CategoryRepositoryInterface;

class Data extends AbstractHelper
{
    const XML_PATH_ENABLED = 'payment/coinpayment/active';
    
    /**
    * @var \Magento\Framework\App\Config\ScopeConfigInterface
    */
	protected $_objectmanager;
	protected $assetRepo;
	protected $categoryRepository;
	protected $_storeManager;
	protected $_categoryFactory;
	protected $_category;
	protected $_setFactory;
	
    /**
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Context $context,
		ObjectManagerInterface $objectmanager,
		\Magento\Framework\View\Asset\Repository $assetRepo,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Catalog\Model\CategoryFactory $categoryFactory,
		CategoryRepositoryInterface $categoryRepository,
		\Magento\Eav\Model\Entity\Attribute\SetFactory $setFactory
    ) 
	{
        parent::__construct($context);
		$this->assetRepo = $assetRepo;
		$this->_storeManager = $storeManager;
		$this->categoryRepository = $categoryRepository;
		$this->_objectmanager = $objectmanager;
		$this->_setFactory = $setFactory;
		$this->_categoryFactory = $categoryFactory;
        $config = array(
            "environment" => $this->getPaymentMode(),
            "userid" => $this->getApiUserName(),
            "password" => $this->getApiPassword(),
            "signature" => $this->getApiSignature(),
            "appid" => $this->getApplicationId(), # You can set this when you go live
        );
        $this->config = $config;
    }
    public function isActive($store = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }
    private $urls = array(
        "sandbox" => array(
            "api"      => "",
            "redirect" => "",
        ),
        "live" => array(
            "api"      => "",
            "redirect" => "",
        )
    );
    public function call($options = [], $method) {
        $this->prepare($options);
        return $this->_curl($this->api_url($method), $options, $this->headers($this->config));
    }
    public function redirect($response) {
        if(@$response["payKey"]) $redirect_url = sprintf("%s?cmd=_ap-payment&paykey=%s", $this->redirect_url(), $response["payKey"]);
        else $redirect_url = sprintf("%s?cmd=_ap-preapproval&preapprovalkey=%s", $this->redirect_url(), $response["preapprovalKey"]);
        return $redirect_url;
    }
    private function redirect_url() {
        return $this->urls[$this->config["environment"]]["redirect"];
    }
    private function api_url($method) {
        return $this->urls[$this->config["environment"]]["api"].$method;
    }
    private function _curl($url, $values, $header) {
        $curl = curl_init($url);
        
        $options = array(
            CURLOPT_HTTPHEADER      => $header,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_SSL_VERIFYPEER  => false,
            CURLOPT_SSL_VERIFYHOST  => false,
            CURLOPT_POSTFIELDS  => json_encode($values),
            CURLOPT_CUSTOMREQUEST  => "POST",
            CURLOPT_TIMEOUT        => 10
        );
        
        curl_setopt_array($curl, $options);
        $rep = curl_exec($curl);
        
        $response = json_decode($rep, true);
        curl_close($curl);
        
        return $response;
    }
    private function prepare(&$options) {
        $this->expand_urls($options);
        $this->merge_defaults($options);
    }
    private function expand_urls(&$options) {
        $regex = '#^https?://#i';
        if(array_key_exists('returnUrl', $options) && !preg_match($regex, $options['returnUrl'])) {
            $this->expand_url($options['returnUrl']);
        }
        
        if(array_key_exists('cancelUrl', $options) && !preg_match($regex, $options['cancelUrl'])) {
            $this->expand_url($options['cancelUrl']);
        }
    }
    private function expand_url(&$url) {
        $current_host = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://{$_SERVER["HTTP_HOST"]}";
        if(preg_match("#^/#i", $url)) {
            $url = $current_host.$url;
        }else {
            $directory = dirname($_SERVER['PHP_SELF']);
            $url = $current_host.$directory.$url;
        }
    }
    private function merge_defaults(&$options) {
        $defaults = array(
            'requestEnvelope' => array(
                'errorLanguage' => 'en_US',
            )
        );
        $options = array_merge($defaults, $options);
    }
}