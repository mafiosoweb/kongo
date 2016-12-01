<?php
/**
 * Magento Module developed by NoStress Commerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@nostresscommerce.cz so we can send you a copy immediately.
 *
 * @copyright Copyright (c) 2015 NoStress Commerce (http://www.nostresscommerce.cz)
 *
 */

namespace Nostress\Koongo\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
	public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
	{
		$installer = $setup;

        $installer->startSetup();

		//handle all possible upgrade versions

		//if(!$context->getVersion()) {
			//no previous version found, installation, InstallSchema was just executed
			//be careful, since everything below is true for installation !
		//}

		if (version_compare($context->getVersion(), '2.0.1') < 0) 
		{
			$this->updateToVersion201($installer);
		}		

		 $installer->endSetup();
	}
	
	protected function updateToVersion201($installer)
	{
		$table = $installer->getConnection()
		->newTable($installer->getTable('nostress_koongo_taxonomy_category_mapping'))
		->addColumn('entity_id', Table::TYPE_INTEGER, 11,  ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],'Entity Id')
		->addColumn('taxonomy_code', Table::TYPE_TEXT, 255,  ['nullable' => false],'Taxonomy code')
		->addColumn('locale', Table::TYPE_TEXT, 255,  ['nullable' => false, 'default' => 'en_UK' ],'Locale')
		->addColumn('store_id', Table::TYPE_SMALLINT, 5,  ['unsigned' => true, 'nullable' => false, 'default' => '0'],'Store Id')
		->addColumn('config', Table::TYPE_TEXT, null, ['nullable' => false])
		->setComment('Taxonomy categories mapping rules table');
		$installer->getConnection()->createTable($table);
			
		$table = $installer->getConnection()
		->newTable($installer->getTable('nostress_koongo_cache_channelcategory'))
		->addColumn('profile_id', Table::TYPE_INTEGER, 10,  ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],'Profile Id')
		->addColumn('product_id', Table::TYPE_INTEGER, 10,  ['unsigned' => true, 'nullable' => false, 'primary' => true],'Product Id')
		->addColumn('hash', Table::TYPE_TEXT, 255,  ['nullable' => false ],'Taxonomy category path hash')
		->addIndex($installer->getIdxName('cache_channelcategory', ['hash',]), ['hash'])
		->addForeignKey(
				$installer->getFkName(
						'nostress_koongo_cache_channelcategory',
						'profile_id',
						'nostress_koongo_channel_profile',
						'entity_id'
				),
				'profile_id',
				$installer->getTable('nostress_koongo_channel_profile'),
				'entity_id',
				\Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
		)
		->setComment('Cache table for profile channel categories');
		$installer->getConnection()->createTable($table);
	}
}