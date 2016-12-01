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
* Category loader for export process
* @category Nostress 
* @package Nostress_Koongo
* 
* 
* List of new required params:
* media_url, store_locale, store_language, store_country, current_date, current_datetime, current_time
* 
*/

namespace Nostress\Koongo\Model\Data\Loader;

class Category  extends \Nostress\Koongo\Model\Data\Loader 
{   
	public function __construct(\Magento\Catalog\Model\Indexer\Category\Flat\State $categoryFlatState,
			\Magento\Catalog\Model\Indexer\Product\Flat\State $productFlatState)
	{
		parent::__construct($categoryFlatState,$productFlatState);
		$this->_init('Nostress\Koongo\Model\ResourceModel\Data\Loader\Category');
	}	
    
    public function initAdapter()
    {
        parent::initAdapter();                
        $this->basePart();
//         echo $this->adapter->getSelect()->__toString();
//         exit();
    } 
  
    
    //***************************BASE PART**************************************    
    protected function basePart()
    {
    	$this->adapter->joinParentCategory();    	
        $this->adapter->joinCategoryPath();
    	$this->adapter->orderByLevel();
    	$this->adapter->addCategoryFilter();  	
    }
    
    
    //***************************COMMON PART**************************************
    
    protected function commonPart()
    {
    	
    } 
}
?>