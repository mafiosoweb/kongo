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
* Data loader for export process
* @category Nostress 
* @package Nostress_Koongo
* 
* 
* List of new required params:
* media_url, store_locale, store_language, store_country, current_date, current_datetime, current_time
* 
*/

namespace Nostress\Koongo\Model\Data;

abstract class Loader  extends \Nostress\Koongo\Model\AbstractModel 
{   
	protected $adapter;		
	/**
	 * @var \Indexer\Product\Flat\State
	 */
	protected $productFlatState;
	/**
	 * @var \Indexer\Category\Flat\State
	 */
	protected $categoryFlatState;
	
	public function __construct(\Magento\Catalog\Model\Indexer\Category\Flat\State $categoryFlatState,
			\Magento\Catalog\Model\Indexer\Product\Flat\State $productFlatState)
	{
		$this->_init('Nostress\Koongo\Model\ResourceModel\Data\Loader');
		$this->categoryFlatState = $categoryFlatState;
		$this->productFlatState = $productFlatState;
	}
	
	public function init($params)
	{		
		$this->setData($params);
		$this->initAdapter();
	}
	
	public function loadBatch()
	{
		return $this->adapter->loadBatch();
	}
	
	public function loadAll()
	{
		return $this->adapter->loadAll();
	}
	
	protected function initAdapter()
	{		
		$this->adapter = $this->_getResource(); 				
 		$this->adapter->setStoreId($this->getStoreId());
 		$this->adapter->setProfileId($this->getProfileId());
 		$this->adapter->setAttributes($this->getAttributes());
 		$data = $this->getData();
 		unset($data["store_id"]);
 		unset($data["profile_id"]);
 		unset($data["attributes"]);
 		$this->adapter->setData($data); 		 		
 		$this->adapter->init();
	}		
}
?>