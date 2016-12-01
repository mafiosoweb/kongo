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
 * BLock for attribute settings form options tab
 *
 * @category Nostress
 * @package Nostress_Koongo
 *
 */
namespace Nostress\Koongo\Block\Adminhtml\Channel\Profile\General\Edit\Tab\Main;

use Nostress\Koongo\Model\Channel\Profile;

class Attributes extends \Magento\Backend\Block\Template
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     *  @var $model \Nostress\Koongo\Model\Channel\Profile
     **/
    protected $profile;
    
    /**
     * @var string
     */
    protected $_template = 'Nostress_Koongo::koongo/channel/profile/general/main/attributes.phtml';

    protected $_tooltip = 'attributes_mapping_attributes_mapping';
    /*
     * @var \Nostress\Koongo\Model\Config\Source\Attributes
    */
    protected $attributeSource;
    
    /**
     * @var \Magento\Framework\Validator\UniversalFactory $universalFactory
     */
    protected $_universalFactory;

    /**
     * Attriutbes array
     * @var unknown_type
     */
    protected $_attributes;
    
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Validator\UniversalFactory $universalFactory
     * @param \Nostress\Koongo\Model\Config\Source\Attributes $attributeSource
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Validator\UniversalFactory $universalFactory,
    	\Nostress\Koongo\Model\Config\Source\Attributes $attributeSource,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_registry = $registry;
        $this->_universalFactory = $universalFactory;
        $this->profile = $this->_registry->registry('koongo_channel_profile');
        $this->attributeSource = $attributeSource;
    }

    /**
     * {@inheritdoc}
     */
    public function getComponentName()
    {
    	if (null === $this->getData('component_name')) {
    		$this->setData('component_name', $this->getNameInLayout());
    	}
    	return $this->getData('component_name');
    }
    
    public function getChannelLabel()
    {
    	return $this->profile->getFeed()->getChannel()->getLabel();
    }
    
    public function getStandardAttributesItems()
    {
    	if(!isset($this->_attributes))
    	{
    		$collection = $this->getChildBlock('attributes_table_grid')->getCollection();
	    	$attributes = [];
	    	foreach($collection as $item)
		    {
		    	$attributes[] = $item->getData();
		    }
	    	$this->_attributes = $attributes;
    	}
    	return $this->_attributes;
    }
    
    public function getStandardAttributesJson()
    {
    	$attributes = $this->getStandardAttributesItems();
    	return json_encode($attributes);
    }

    public function getTooltip() {
        
        return $this->profile->helper->renderTooltip( $this->_tooltip);
    }
    
    public function getHelp( $key) {
    
        return $this->profile->helper->getHelp( $key);
    }
}
