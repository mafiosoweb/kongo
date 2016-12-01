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
 * Rule Product Condition data model
 *
 * @category Nostress
 * @package Nostress_Koongo
 *
 */

namespace Nostress\Koongo\Model\Rule\Condition;

/**
 * Class Product
 */
class Product extends \Magento\CatalogRule\Model\Rule\Condition\Product
{	
	/**
	 * Default table alias for sql condition generation
	 * @var unknown_type
	 */
	const DEFAULT_TABLE_ALIAS = 'default_table_alias';
	/*
	 * @var \Nostress\Koongo\Model\Config\Source\Attributes
	*/
	protected $attributeSource;
	
	/**
	 * Core registry
	 *
	 * @var \Magento\Framework\Registry
	 */
	protected $_coreRegistry;
	
	/**
	 * @param \Magento\Rule\Model\Condition\Context $context
	 * @param \Magento\Backend\Helper\Data $backendData
	 * @param \Magento\Eav\Model\Config $config
	 * @param \Magento\Catalog\Model\ProductFactory $productFactory
	 * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
	 * @param \Magento\Catalog\Model\ResourceModel\Product $productResource
	 * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection $attrSetCollection
	 * @param \Magento\Framework\Locale\FormatInterface $localeFormat
	 * @param \Nostress\Koongo\Model\Config\Source\Attributes $attributeSource
	 * @param  \Magento\Framework\Registry $registry
	 * @param array $data
	 */
	public function __construct(
			\Magento\Rule\Model\Condition\Context $context,
			\Magento\Backend\Helper\Data $backendData,
			\Magento\Eav\Model\Config $config,
			\Magento\Catalog\Model\ProductFactory $productFactory,
			\Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
			\Magento\Catalog\Model\ResourceModel\Product $productResource,
			\Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection $attrSetCollection,
			\Magento\Framework\Locale\FormatInterface $localeFormat,
			\Nostress\Koongo\Model\Config\Source\Attributes $attributeSource,
			\Magento\Framework\Registry $registry,
			array $data = []
	) 
	{		
		$this->attributeSource = $attributeSource;
		$this->_coreRegistry = $registry;
		parent::__construct( $context, $backendData, $config, $productFactory, $productRepository, $productResource, $attrSetCollection, $localeFormat, $data);
	}
	
	/**
	 * Load attribute options
	 *
	 * @return $this
	 */
	public function loadAttributeOptions()
	{
		$productAttributes = $this->_productResource->loadAllAttributes()->getAttributesByCode();
	
		/* @var $model \Nostress\Koongo\Model\Channel\Profile */
		$profile = $this->_coreRegistry->registry('koongo_channel_profile');
		//$currentStoreId = $this->_coreRegistry->registry('koongo_current_store_id');
		
		$taxonomyLabel = "";
		$storeId = \Magento\Store\Model\Store::DEFAULT_STORE_ID;
		if(!empty($profile))
		{
			$storeId = $profile->getStoreId();
			$taxonomyLabel = $profile->getTaxonomyLabel();
		}
		
		$moduleProductAttributes =  $this->attributeSource->toIndexedArray($storeId, $taxonomyLabel,true);
		
		$attributes = [];
		foreach ($productAttributes as $attribute) {
			/* @var $attribute \Magento\Catalog\Model\ResourceModel\Eav\Attribute */
			
			$attributeCode = $attribute->getAttributeCode();
			
			if(!isset($moduleProductAttributes[$attributeCode]))
				continue;
			
			$attributes[$attributeCode] = $moduleProductAttributes[$attributeCode];
			unset($moduleProductAttributes[$attributeCode]);
		}
	
		//
		$attributes = array_merge($attributes,$moduleProductAttributes);
		$this->_addSpecialAttributes($attributes);
	
		asort($attributes);
		$this->setAttributeOption($attributes);
	
		return $this;
	}
	
	/**
	 * Add special attributes
	 *
	 * @param array $attributes
	 * @return void
	 */
	protected function _addSpecialAttributes(array &$attributes)
	{
		parent::_addSpecialAttributes($attributes);
// 		$attributes['price_final_include_tax'] = __('Price Final Include Tax');
	}
	
	protected function _getAttributeAliases( $attribute, $columns) 
	{
		//$columns['default_table_alias']
		foreach( $columns as $tableAlias => $tableColumns) 
		{
			if($tableAlias == self::DEFAULT_TABLE_ALIAS)
				continue;
			foreach( $tableColumns as $columnAlias => $columnValue) 
			{												
				if( $attribute == $columnAlias) 
				{
					//If column value contains composed value - it means it contains table alias inside
					if( strpos( $columnValue, $tableAlias) !== false) 
					{
						return $columnValue;
					} 
					else 
					{
						return $tableAlias.".".$columnValue;
					}
				}
			}
		}
		return $columns[self::DEFAULT_TABLE_ALIAS].".".$attribute;
	}
	
	/**
	 * @param Zend_Db_Select
	 * @return bool|mixed|string
	 */
	public function asSqlWhere( $columns)
	{
		$attributeCode = $where = $this->getAttribute();		
		$operator = $this->getOperator();
		$value = $this->getValue();
		if (is_array($value)) {
			$ve = addslashes(join(',', $value));
		} else {
			$ve = addslashes($value);
		}
	
		$attributeAlias = $this->_getAttributeAliases($attributeCode, $columns);			
		
		$attribute = $this->_config->getAttribute('catalog_product', $attributeCode);
	
		// whether attribute is multivalue
		$isMultiselect = $attribute->getId() && ($attribute->getFrontendInput() == 'multiselect');
	
		//category_ids exception, IS and IS NOT must be evaluates in the same way as IS ONE OF and IS NOT ONE OF
		if($attributeCode == 'category_ids')
		{
			$isMultiselect = true;
			if($operator == "==")
				$operator = "()";
			if($operator == "!=")
				$operator = "!()";
		}				
			
		switch ($operator) {
			case '==':
				$whereTemplate = '{{ta}}'.'='."'{$ve}'";
				break;
			case '!=':
				if(!empty($ve))
					$whereTemplate = '{{ta}}'.'<>'."'{$ve}' OR {{ta}} IS NULL";
				else
					$whereTemplate = '{{ta}}'.'<>'."'' AND {{ta}} IS NOT NULL";
				break;
	
			case '>=': case '<=': case '>': case '<':
				$whereTemplate = "{{ta}}{$operator}'{$ve}'";
				break;
	
			case '{}':
				$whereTemplate = "{{ta}} LIKE '%{$ve}%'";
				break;
			case '!{}':
				$whereTemplate = "{{ta}} NOT LIKE '%{$ve}%' OR {{ta}} IS NULL";
				break;
	
				case '()':
				$valueArray = preg_split('|\s*,\s*|', $ve);
				if (!$isMultiselect) 
				{
					$whereTemplate = "{{ta}} IN ('".join("','", $valueArray)."')";
				} 
				else 
				{
					$whereItems = array();
					foreach ($valueArray as $valueItem) 
					{
						$whereItems[] = "find_in_set('".addslashes($valueItem)."', {{ta}})";
					}
					$whereTemplate = '('.join(') OR (', $whereItems).')';
				}
				break;
			case '!()':
				$valueArray = preg_split('|\s*,\s*|', $ve);
				if (!$isMultiselect) 
				{
					$whereTemplate = "{{ta}} NOT IN ('".join("','", $valueArray)."')";
				} 
				else 
				{
					$whereItems = array();
					foreach ($valueArray as $valueItem) 
					{
						$whereItems[] = "!find_in_set('".addslashes($valueItem)."', {{ta}})";
					}
					$whereTemplate = '('.join(') AND (', $whereItems).')';
				}
				$whereTemplate = "({$whereTemplate}) OR {{ta}} IS NULL";
	
				break;
	
			default:
				return false;
		}
	
		$where = str_replace('{{ta}}', $attributeAlias, $whereTemplate);
	
		return $where;
    }
}
