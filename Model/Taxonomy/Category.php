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
* Class for taxonomy
*
* @category Nostress
* @package Nostress_Koongo
*
*/

namespace Nostress\Koongo\Model\Taxonomy;

class Category  extends \Nostress\Koongo\Model\AbstractModel 
{
	const ALL_LOCALES = 'all';
	const ROOT = 'taxonomy';
	
// 	const SRC = 'src';
// 	const PATH = 'path';
// 	const FILENAME = 'filename';
	//const DOWNLOAD = 'download';
	
	//config tags
	//const LOCALE = 'locale';
// 	const DELIMITER = 'delimiter';
// 	const VARIABLE = 'variable';
// 	//const DEFAULT_LOCALE = 'default';
// 	const TRANSLATE = 'rewrite';
// 	const GENERAL = 'general';
// 	const OPTION =  'option';
// 	const LABEL = 'label';
// 	const VALUE = 'value';
	//const COMMON = 'common';
	
	//columns
	const C_CODE = 'taxonomy_code';
	const C_LOCALE = 'locale';
	const C_NAME = 'name';
	const C_ID = 'id';
	const C_PATH = 'path';
	const C_IDS_PATH = 'ids_path';
	const C_LEVEL = 'level';
	const C_PARENT_NAME = 'parent_name';
	const C_PARENT_ID = 'parent_id';
	
	const DEFAULT_LOCALE_DELIMITER = "_";
	
	protected $_enginesConfig;
	protected $_message = array(true=>array(),false=>array());
	
	/**
	 * @var \Nostress\Koongo\Model\Taxonomy\SetupFactory
	 */
	protected $taxonomySetupFactory;
	
	/**
	 * @param \Magento\Framework\Model\Context $context
	 * @param \Magento\Framework\Registry $registry
	 * @param \Nostress\Koongo\Helper\Data $helper
	 * @param \Magento\Store\Model\StoreManagerInterface $storeManager
	 * @param \Nostress\Koongo\Model\Translation $translation
	 * @param \Nostress\Koongo\Model\Taxonomy\SetupFactory $taxonomySetupFactory
	 * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
	 * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
	 * @param array $data	
	 */
	public function __construct(
			\Magento\Framework\Model\Context $context,
			\Magento\Framework\Registry $registry,
			\Nostress\Koongo\Helper\Data $helper,
			\Magento\Store\Model\StoreManagerInterface $storeManager,	
			\Nostress\Koongo\Model\Translation $translation,
			\Nostress\Koongo\Model\Taxonomy\SetupFactory $taxonomySetupFactory,
			\Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
			\Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
			array $data = []			
	)
	{
		$this->taxonomySetupFactory = $taxonomySetupFactory;
		parent::__construct($context, $registry, $helper, $storeManager, $translation, $resource, $resourceCollection, $data);
	}
	
	public function _construct()
	{		
		$this->_init('Nostress\Koongo\Model\ResourceModel\Taxonomy\Category');
	}
	
	public function getCategories($code,$locale,$defaultLocale, $select = null,$indexField = null)
	{		
		$filter = "";
		if($this->countColumns($code,$locale) > 0)
		{
			$filter = $this->getFilterFields($code,$locale);
		}
		else if(!empty($defaultLocale) && $this->countColumns($code,$defaultLocale) > 0) //google has no defined taxonomy for all other locales
		{
			$filter = $this->getFilterFields($code,$defaultLocale);
		}
		else
		{
			$filter = $this->getFilterFields($code);
		}
		 
		$items = $this->_getTaxonomyCategories($filter,$select,$indexField);
		return $items;
	}
	
	public function countColumns($code,$locale = self::ALL_LOCALES)
	{
		return $this->getResource()->countColumns($code,$locale);
	}
	
	public function _getTaxonomyCategories($filter = null,$select = null, $indexField = null)
	{
		$collection = $this->getCollection();
		$collection->addFieldsToFilter($filter);
		$collection->addFieldsToSelect($select);
		$select = $collection->getSelect();//init select don't delete
		$select->order('path');
// 		echo $select->__toString();
// 		exit();
		$collection->load();
		return $collection->getItems($indexField);
	}		
	
	protected function getFilterFields($code,$locale = self::ALL_LOCALES)
	{
		$fields = array();
		$fields[self::C_CODE] = $code;
		$fields[self::C_LOCALE] = $locale;
		return $fields;
	}	
}