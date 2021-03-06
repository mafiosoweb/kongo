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
 * Shipping cost intervals add/edit form options tab
 *
 * @category Nostress
 * @package Nostress_Koongo
 *
 */
namespace Nostress\Koongo\Block\Adminhtml\Channel\Profile\General\Edit\Tab\Shippingcost;

use Nostress\Koongo\Model\Channel\Profile;

class Intervals extends \Magento\Backend\Block\Template
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @var string
     */
    protected $_template = 'Nostress_Koongo::koongo/channel/profile/general/shipping/intervals.phtml';

    /**
     * @var \Magento\Framework\Validator\UniversalFactory $universalFactory
     */
    protected $_universalFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Validator\UniversalFactory $universalFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Validator\UniversalFactory $universalFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_registry = $registry;
        $this->_universalFactory = $universalFactory;
    }

    
    public function getShippingConfigPath()
    {
    	return Profile::CONFIG_FEED.'['.Profile::CONFIG_COMMON.']'.'[shipping]';
    }
    
	public function getCostIntervals()
	{
		$model = $this->_registry->registry('koongo_channel_profile');
		$config = $model->getConfigItem(Profile::CONFIG_FEED,true,Profile::CONFIG_COMMON);
		$intervals = [];
		
		if(isset($config['shipping']['cost_setup']))
			$intervals = $config['shipping']['cost_setup'];
		
		$values = [];
		$index = 0;
		foreach($intervals as $key => $value)
		{
			$value["id"] = "option_".$index;
			$value["sort_order"] = $index;
			$values[] = $value;
			$index++;
		}
		return $values;
	}
	
}
