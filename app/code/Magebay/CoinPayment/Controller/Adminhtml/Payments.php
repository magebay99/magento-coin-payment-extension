<?php
/**
 * @Author      Magebay
 * @package     Magebay_CoinPayment
 * @copyright   Copyright (c) 2017 MAGEBAY (https://www.magebay.com)
 * @terms       https://www.magebay.com/terms
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 **/
namespace Magebay\CoinPayment\Controller\Adminhtml;

class Payments extends Paymentactions
{
	/**
	 * Form session key
	 * @var string
	 */
    protected $_formSessionKey  = 'coinpayment_payments_form_data';

    /**
     * Allowed Key
     * @var string
     */
    protected $_allowedKey      = 'Magebay_CoinPayment::manage_payments';

    /**
     * Model class name
     * @var string
     */
    protected $_modelClass      = 'Magebay\CoinPayment\Model\Payments';

    /**
     * Active menu key
     * @var string
     */
    protected $_activeMenu      = 'Magebay_CoinPayment::manage_payments';

    /**
     * Status field name
     * @var string
     */
    protected $_statusField     = 'status';
}