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

/**
		DELETE FROM setup_module WHERE `setup_module`.`module` = 'Nostress_Koongo';
		DROP TABLE nostress_koongo_cache_product;
		DROP TABLE nostress_koongo_cache_tax;
		DROP TABLE nostress_koongo_cache_categorypath;
		DROP TABLE nostress_koongo_cache_weee;
		DROP TABLE nostress_koongo_cache_profilecategory;
		DROP TABLE nostress_koongo_cache_channelcategory;
		DROP TABLE nostress_koongo_taxonomy_category_mapping;
		DROP TABLE nostress_koongo_taxonomy_setup;
		DROP TABLE nostress_koongo_cron;
		DROP TABLE nostress_koongo_channel_profile;
		DROP TABLE nostress_koongo_channel_feed;
		DROP TABLE nostress_koongo_taxonomy_category;
 
*/

namespace Nostress\Koongo\Setup;

class Uninstall implements \Magento\Framework\Setup\UninstallInterface
{
	/**
	 * Module uninstall code
	 *
	 * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
	 * @param \Magento\Framework\Setup\ModuleContextInterface $context
	 * @return void
	 */
	public function uninstall(
			\Magento\Framework\Setup\SchemaSetupInterface $setup,
			\Magento\Framework\Setup\ModuleContextInterface $context
	) {
		$setup->startSetup();		
		$connection = $setup->getConnection();
		
		$connection->dropTable('nostress_koongo_cache_product');
		$connection->dropTable('nostress_koongo_cache_tax');
		$connection->dropTable('nostress_koongo_cache_categorypath');		
		$connection->dropTable('nostress_koongo_cache_weee');
		$connection->dropTable('nostress_koongo_cache_profilecategory');
		$connection->dropTable('nostress_koongo_taxonomy_setup');
		$connection->dropTable('nostress_koongo_cron');
		$connection->dropTable('nostress_koongo_channel_profile');
		$connection->dropTable('nostress_koongo_channel_feed');
		$connection->dropTable('nostress_koongo_taxonomy_category');		

		$setup->endSetup();
	}
}