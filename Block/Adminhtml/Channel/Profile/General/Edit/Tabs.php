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
 * Admin page left menu
 *
 * @category Nostress
 * @package Nostress_Koongo
 *
 */

namespace Nostress\Koongo\Block\Adminhtml\Channel\Profile\General\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
	const BASIC_TAB_GROUP_CODE = 'basic';
	const ADVANCED_TAB_GROUP_CODE = 'advanced';
	
	/**
	 * @var string
	 */
	protected $_template = 'Magento_Catalog::product/edit/tabs.phtml';
	
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('page_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Basic Settings'));
    }
    
    /**
     * Check whether active tab belong to advanced group
     *
     * @return bool
     */
    public function isAdvancedTabGroupActive()
    {
    	return $this->_tabs[$this->_activeTab]->getGroupCode() == self::ADVANCED_TAB_GROUP_CODE;
    }
    
    /**
     * @return $this
     */
    protected function _beforeToHtml()
    {
    	$this->addTab(
    		'main',
    		[
    			'label' => __('Attributes Mapping'),
    			'title' => __('Attributes Mapping'),
    			'active' => true,
    			'group_code' => self::BASIC_TAB_GROUP_CODE,
    			'content' => $this->getChildHtml('main')
    		]
    	);
    	$this->addTab(
    		'shipping',
    		[
    			'label' => __('Shipping Cost'),
    			'title' => __('Shipping Cost'),
    			//'active' => true,
    			'group_code' => self::BASIC_TAB_GROUP_CODE,
    			'content' => $this->getChildHtml('shipping_cost')
    		]
    	);
    	
    	$this->addTab(
    		'stock',
    		[
    			'label' => __('Stock Status Values'),
    			'title' => __('Stock Status Values'),
    			//'active' => true,
    			'group_code' => self::ADVANCED_TAB_GROUP_CODE,
    			'content' => $this->getChildHtml('stock')
    		]
    	);
    	
    	$this->addTab(
    		'price',
    		[
    			'label' => __('Price and Date'),
    			'title' => __('Price and Date'),
    			//'active' => true,
    			'group_code' => self::ADVANCED_TAB_GROUP_CODE,
    			'content' => $this->getChildHtml('price')
    		]
    	);
    	
    	$this->addTab(
    		'file',
    		[
    			'label' => __('Feed File'),
    			'title' => __('Feed File'),
    			//'active' => true,
    			'group_code' => self::ADVANCED_TAB_GROUP_CODE,
    			'content' => $this->getChildHtml('file')
    		]
    	);
    	
    	$this->addTab(
    		'sort',
    		[
    			'label' => __('Sort Products'),
    			'title' => __('Sort Products'),
    			//'active' => true,
    			'group_code' => self::ADVANCED_TAB_GROUP_CODE,
    			'content' => $this->getChildHtml('sort')
    		]
    	);
    	
    	$this->addTab(
    		'category',
    		[
    			'label' => __('Category'),
    			'title' => __('Category'),
    			//'active' => true,
    			'content' => $this->getChildHtml('category'),
    			'group_code' => self::ADVANCED_TAB_GROUP_CODE
    		]
    	);
    
    	return parent::_beforeToHtml();
    }
}
