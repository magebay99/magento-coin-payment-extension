<?php
/**
 * @Author      Magebay
 * @package     Magebay_CoinPayment
 * @copyright   Copyright (c) 2017 MAGEBAY (https://www.magebay.com)
 * @terms       https://www.magebay.com/terms
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 **/
namespace Magebay\CoinPayment\Setup;
 
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
 
class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        // Check if the table magebay_coin_payment already exists
        if ($installer->getConnection()->isTableExists($installer->getTable('magebay_coin_payment')) != true) {
            // Create magebay_coin_payment table
            $table = $installer->getConnection()->newTable(
                $installer->getTable('magebay_coin_payment')
            )->addColumn(
                    'id',
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true
                    ],
                    'ID'
                )
                ->addColumn(
                    'name',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => ''],
                    'Name'
                )
                ->addColumn(
                    'code',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => ''],
                    'Code'
                )
                ->addColumn(
                    'coin_address',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => ''],
                    'Coin Address'
                )
                ->addColumn(
                    'auto_rate',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false],
                    'Auto Rate'
                )
                ->addColumn(
                    'rate',
                    Table::TYPE_FLOAT,
                    null,
                    ['nullable' => false],
                    'Rate'
                )
                ->addColumn(
                    'fee',
                    Table::TYPE_FLOAT,
                    null,
                    ['nullable' => false],
                    'Fee'
                )
                ->addColumn(
                    'description',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => ''],
                    'Description'
                )
                ->addColumn(
                    'note',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => ''],
                    'Note'
                )
                ->addColumn(
                    'sortorder',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false],
                    'Sort Order'
                )
                ->addColumn(
                    'status',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false],
                    'Status'
                )
                ->setComment('Magebay Coin Payment Table')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
            $installer->getConnection()->createTable($table);
        }
        $installer->endSetup();
    }
}