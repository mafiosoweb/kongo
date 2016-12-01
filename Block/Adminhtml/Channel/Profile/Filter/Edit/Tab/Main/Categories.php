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
 * Block for category filter settings
 *
 * @category Nostress
 * @package Nostress_Koongo
 *
 */
namespace Nostress\Koongo\Block\Adminhtml\Channel\Profile\Filter\Edit\Tab\Main;

use Nostress\Koongo\Model\Channel\Profile;

class Categories extends \Magento\Backend\Block\Template
{
    protected $_tooltip = 'filter_categories';
    
    /**
     * @var string
     */
    protected $_template = 'Nostress_Koongo::koongo/channel/profile/filter/main/categories.phtml';
    
    /**
     *  @var $model \Nostress\Koongo\Model\Channel\Profile
     **/
    protected $profile;
    
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
    		\Magento\Backend\Block\Template\Context $context,
    		\Magento\Framework\Registry $registry,
    		array $data = []
    ) {
    	parent::__construct($context, $data);
    	$this->_registry = $registry;
    	$this->profile = $this->_registry->registry('koongo_channel_profile');
    }
    
    public function getCategoryTreeHtml()
    {
    	$treeBlock = $this->getChildBlock("category_checkboxes_tree");
    	$treeBlock->setStore($this->profile->getStoreId());
    	$treeBlock->setJsFormObject($this->getFieldsetName());
    	
    	$categoryIds = $this->getCategoryIdsString();
    	if(empty($categoryIds))
    		$categoryIds = [];
    	else
    		$categoryIds = explode(",", $categoryIds);
    	$treeBlock->setCategoryIds($categoryIds);
    	
    	$html = $treeBlock->toHtml();
    	//adjust value to own input element, instead of js object
    	return str_replace($this->getFieldsetName().".updateElement.value = this.getChecked().join(', ');", "jQuery('#{$this->getCategoriesFilterInputId()}').val(this.getChecked().join(', '));", $html);
    }
    
    public function getCategoryIdsString()
    {
    	$categoryIds = $this->profile->getConfigItem(Profile::CONFIG_FILTER,false,Profile::CONFIG_FILTER_CATEGORIES);
    	if(!isset($categoryIds))
    		$categoryIds = "";
    	return $categoryIds;
    }
    
    public function getFieldsetName()
    {
    	return "categories_fieldset";
    }
    
    public function getCategoriesFilterInputName()
    {
    	return Profile::CONFIG_FILTER.'[categories]';
    }
    
    public function getCategoriesFilterInputId()
    {
    	return 'nsc_koongo_filter_categories';
    }

    public function getTooltip() {
    
        return $this->profile->helper->renderTooltip( $this->_tooltip);
    }
}
