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

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Upgrade Data script
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var \Nostress\Koongo\Model\Api\Client\Simple
     */
    protected $_client;
    
    /**
     * Init
     *
     * @param CategorySetupFactory $categorySetupFactory
     */
    public function __construct(
        \Nostress\Koongo\Model\Api\Client\Simple $client
    )
    {
        $this->_client = $client;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $this->_client->updateServerConfig( true);


//        $setup->getConnection()
//            ->insertForce($setup->getTable('nostress_koongo_channel_feed'), $bind);
        
        $setup->endSetup();
    }
}
