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
* Class for taxonomy category management
*
* @category Nostress
* @package Nostress_Koongo
*
*/

namespace Nostress\Koongo\Model\Taxonomy\Category;

class Mapping  extends \Nostress\Koongo\Model\AbstractModel
{
	const COL_TAXONOMY_CODE = 'taxonomy_code';
	const COL_LOCALE = 'locale';
	const COL_STORE_ID = 'store_id';
	const COL_CONFIG = "config";
	const CONFIG_RULES = 'rules';
	
	public function _construct()
	{
		$this->_init('Nostress\Koongo\Model\ResourceModel\Taxonomy\Category\Mapping');
	}
	
	public function getMapping($taxonomyCode,$locale,$storeId)
	{
		$collection = $this->getCollection();
		$collection->addFieldToFilter(self::COL_TAXONOMY_CODE,$taxonomyCode);
		$collection->addFieldToFilter(self::COL_LOCALE,$locale);
		$collection->addFieldToFilter(self::COL_STORE_ID,$storeId);
		$collection->getSelect();
		$collection->load();
		foreach($collection as $item)
		{
			return $item;
		}
		return null;
	}	
	
	public function getRules()
	{
		$config = $this->getConfigDecoded();
		$rules = [];
		if(isset($config[self::CONFIG_RULES]))
			$rules = $config[self::CONFIG_RULES];
		return $rules;
	}
	
	protected function getConfigDecoded()
	{
		return json_decode($this->getConfig(),true);
	}
	
}