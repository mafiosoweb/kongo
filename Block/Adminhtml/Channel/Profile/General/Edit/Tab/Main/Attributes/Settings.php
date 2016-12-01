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
 * BLock for attribute settings modal window
 *
 * @category Nostress
 * @package Nostress_Koongo
 *
 */
namespace Nostress\Koongo\Block\Adminhtml\Channel\Profile\General\Edit\Tab\Main\Attributes;

use Nostress\Koongo\Model\Channel\Profile;

class Settings extends \Magento\Backend\Block\Template
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     *  @var $model \Nostress\Koongo\Model\Channel\Profile
     **/
    protected $profile;
    
    /*
     * @var \Nostress\Koongo\Model\Config\Source\Attributes
    */
    protected $attributeSource;
    
    /*
     * @var \Nostress\Koongo\Model\Config\Source\Macros
    */
    protected $macroSource;
    
    /*
     * @var \Nostress\Koongo\Model\Config\Source\Postprocactions
    */
    protected $postprocSource;
        
    /**
     * @var string
     */
    protected $_template = 'Nostress_Koongo::koongo/channel/profile/general/main/attributes/settings.phtml';
    
    protected $_tooltip = 'attributes_advanced_settings';

    /**
     * @var \Magento\Framework\Validator\UniversalFactory $universalFactory
     */
    protected $_universalFactory;
    
    /*
     * @var Array
    */
    protected $_magentoAttributeOptions;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Validator\UniversalFactory $universalFactory
     * @param \Nostress\Koongo\Model\Config\Source\Attributes $attributeSource
     * @param \Nostress\Koongo\Model\Config\Source\Macros $macroSource
     * @param \Nostress\Koongo\Model\Config\Source\Postprocactions $postprocSource
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Validator\UniversalFactory $universalFactory,
    	\Nostress\Koongo\Model\Config\Source\Attributes $attributeSource,
    	\Nostress\Koongo\Model\Config\Source\Macros	$macroSource,
    	\Nostress\Koongo\Model\Config\Source\Postprocactions $postprocSource,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_registry = $registry;
        $this->_universalFactory = $universalFactory;
        $this->profile = $this->_registry->registry('koongo_channel_profile');
        $this->attributeSource = $attributeSource;
        $this->postprocSource = $postprocSource;
        $this->macroSource = $macroSource;
    }

    public function getChannelLabel()
    {
    	return $this->profile->getFeed()->getChannel()->getLabel();
    }
    
    public function getPostprocOptions()
    {
    	return $this->postprocSource->toIndexedArray();
    }
    
    /**
     * Select field magento attribute HTML with selected value.
     *
     * @param Attribute $attribute
     * @param mixed $value
     * @return \Magento\Framework\Phrase|string
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function getAttributeSelectHtml()
    {
    	if(!isset($this->_magentoAttributeOptions))
    		$this->_magentoAttributeOptions = $this->attributeSource->toIndexedArray($this->profile->getStoreId(),$this->profile->getTaxonomyLabel());
    	 
    	$options = $this->_magentoAttributeOptions;
    	 
    	if ($size = count($options))
    	{
    		array_unshift($options, ['value' => '', 'label' => __("Please select attribute.")]);
    
    		$arguments = [
    		'name' => "attribute_settings_modal_magento_attribute",
    		'id' => "attribute_settings_modal_magento_attribute",
    		'class' => 'admin__control-select select select-attributes-magento',
    		'extra_params' => "data-bind='value: attributes()[currentAttributeIndex()].magento'"
    		];
    		/** @var $selectBlock \Magento\Framework\View\Element\Html\Select */
    		$selectBlock = $this->_layout->createBlock(
    				'Magento\Framework\View\Element\Html\Select',
    				'',
    				['data' => $arguments]
    		);
    		return $selectBlock->setOptions($options)->getHtml();
    	} else {
    		return __('Attribute load failed.');
    	}
    }
    
    /**
     * Select field magento attribute/macro HTML with selected value.
     *
     * @param Attribute $attribute
     * @param mixed $value
     * @return \Magento\Framework\Phrase|string
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function getCompositionSelectHtml()
    {
    	if(!isset($this->_magentoAttributeOptions))
    		$this->_magentoAttributeOptions = $this->attributeSource->toIndexedArray($this->profile->getStoreId(),$this->profile->getTaxonomyLabel());
    
    	$macroOptions = $this->macroSource->toOptionArray();
  	    	
    	$options = [['value' => '', 'label' => __('-- Please select Macro or Attribute --')]];
    	$options[] = ['label' => __("Macros"), 'value' => $macroOptions,  'optgroup-name' => 'macros'];
    	$options[] = ['label' => __("Magento Attributes"), 'value' => $this->_magentoAttributeOptions,  'optgroup-name' => 'magento_attributes'];

    	if ($size = count($options))
    	{
    		
    		
    		$arguments = [
    		'name' => "attribute_settings_modal_magento_attribute",
    		'id' => "attribute_settings_modal_composed_value",
    		'class' => 'admin__control-select select select-composed-value',
    		'extra_params' => "data-bind=\"value: composedValueSelection, event: { 'change': addSelectionToComposedValue }\""
    				];
    		/** @var $selectBlock \Magento\Framework\View\Element\Html\Select */
    		$selectBlock = $this->_layout->createBlock(
    				'Magento\Framework\View\Element\Html\Select',
    				'',
    				['data' => $arguments]
    		);
    		$html = $selectBlock->setOptions($options)->getHtml();
    		$html .= $this->getTooltip( 'attribute_composition');
    		
    		return $html;
    		
    	} else {
    		return __('Attribute load failed.');
    	}
    }

    public function getTooltip( $key = null) {
    
        if( $key === null) {
            $key = $this->_tooltip;
        }
        
        return $this->profile->helper->renderTooltip( $key);
    }
}
