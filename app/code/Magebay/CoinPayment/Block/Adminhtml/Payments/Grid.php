<?php
/**
 * @Author      Magebay
 * @package     Magebay_CoinPayment
 * @copyright   Copyright (c) 2017 MAGEBAY (https://www.magebay.com)
 * @terms       https://www.magebay.com/terms
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 **/
namespace Magebay\CoinPayment\Block\Adminhtml\Payments;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    protected $moduleManager;
    protected $_paymentsCollection;
    protected $_objectmanager;

    public function __construct(
        \Magento\Framework\Module\Manager $moduleManager,
        \Magebay\CoinPayment\Model\PaymentsFactory $paymentsFactory,
        \Magento\Framework\ObjectManagerInterface $objectmanager,        
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        array $data = []
    ) {
        $this->moduleManager = $moduleManager;
        $this->_paymentsCollection = $paymentsFactory;
        $this->_objectmanager = $objectmanager;        
        parent::__construct($context, $backendHelper, $data);
    }
 
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('paymentsGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setVarNameFilter('payments_record');
    }
 
    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->_paymentsCollection->create()->getCollection();
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }
 
    /**
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'id',
            [
                'header' => __('ID'),
                'type' => 'number',
                'index' => 'id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn(
            'name',
            [
                'header' => __('Name'),
                'index' => 'name',
                'type'   => 'text',
            ]
        );
        $this->addColumn(
            'code',
            [
                'header' => __('Code'),
                'index' => 'code',
                'type'   => 'text',
            ]
        );
        $this->addColumn(
            'auto_rate',
            [
                'header' => __('Auto Rate'),
                'index' => 'auto_rate',
                'type' => 'options',
                'options' => array(
                    '1'=>'Yes',
                    '0'=>'No'
                )
            ]
        );
        $this->addColumn(
            'rate',
            [
                'header' => __('Rate'),
                'index' => 'rate',
                'type'   => 'currency',
                'renderer' => 'Magebay\CoinPayment\Block\Adminhtml\Grid\Column\PaymentsGridRate'
            ]
        );
        $this->addColumn(
            'fee',
            [
                'header' => __('Fee'),
                'index' => 'fee',
                'type'   => 'float',
            ]
        );
        $this->addColumn(
            'description',
            [
                'header' => __('Description'),
                'index' => 'description',
                'type'   => 'text',
            ]
        );
        $this->addColumn(
            'note',
            [
                'header' => __('Note'),
                'index' => 'note',
                'type'   => 'text',
            ]
        );
        $this->addColumn(
            'sortorder',
            [
                'header' => __('Sort Order '),
                'index' => 'sortorder',
                'type'   => 'text',
            ]
        );
        $this->addColumn(
            'status',
            [
                'header' => __('Status'),
                'index' => 'status',
                'type' => 'options',
                'options' => array(
                    '1'=>'Enabled',
                    '0'=>'Disabled'
                ),
                'renderer' => 'Magebay\CoinPayment\Block\Adminhtml\Grid\Column\PaymentsGridStatus'
            ]
        );
        $this->addColumn(
            'edit', [
                'header' => __('Edit'),
                'type' => 'action',
                'getter' => 'getId',
                'actions' => [
                    [
                        'caption' => __('Edit'),
                        'url' => ['base' => '*/*/edit'],
                        'field' => 'id',
                    ],
                ],
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'header_css_class' => 'col-action',
                'column_css_class' => 'col-action',
            ]
        );
        
        //$this->addExportType('*/*/exportCsv', __('CSV'));
        //$this->addExportType('*/*/exportXml', __('XML'));
        //$this->addExportType('*/*/exportExcel', __('Excel'));
        
        $block = $this->getLayout()->getBlock('grid.bottom.links');
        if ($block) {
            $this->setChild('grid.bottom.links', $block);
        }
 
        return parent::_prepareColumns();
    }
    
    /**
     * @return $this
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('id');
        
        $this->getMassactionBlock()->addItem(
            'delete',
            [
                'label' => __('Delete'),
                'url' => $this->getUrl('*/*/delete/', ['_current' => true]),
                'confirm' => "Are you sure you wan't to delete selected coin payment?"
            ]
        );
        $this->getMassactionBlock()->addItem(
            'enable',
            [
                'label' => __('Enable'),
                'url' => $this->getUrl('*/*/massStatus/status/1/', ['_current' => true]),
                'confirm' => "Are you sure you wan't to enable selected coin payment?"
            ]
        );
        $this->getMassactionBlock()->addItem(
            'disable',
            [
                'label' => __('Disable'),
                'url' => $this->getUrl('*/*/massStatus/status/0/', ['_current' => true]),
                'confirm' => "Are you sure you wan't to disable selected payment?"
            ]
        );
 
        return $this;
    }
 
    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', ['_current' => true]);
    }
    
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
    
    public function getpaidStatuses(){
        return array(
            '0'=>'Pending',
            '1'=>'Paid',
            '2'=>'Hold',
            '3'=>'Refunded',
            '4'=>'Voided'
        );
    }
    
    public function getOrderStatuses(){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    	return $objectManager->create('Magento\Sales\Model\Order\Config')->getStatuses();
    }
}