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
 * Abstract Model for Koongo connector cache model
 *
 * @category Nostress
 * @package Nostress_Koongo
 *
 */

namespace Nostress\Koongo\Model\Cache;

class Product  extends \Nostress\Koongo\Model\Cache 
{    	    
    public function _construct()
	{    	
    	$this->_init('Nostress\Koongo\Model\ResourceModel\Cache\Product');
    }
    
    public function setLowestLevel($level)
    {
    	$this->_getResource()->setLowestLevel($level);
    }
    
    public function setExcludedImagesExportEnabled($status)
    {
    	$this->_getResource()->setExcludedImagesExportEnabled($status);
    } 

    public function setAllowInactiveCategoriesExport($status)
    {
    	$this->_getResource()->setAllowInactiveCategoriesExport($status);
    }
    
    public function setStockWebsiteId($stockWebsiteId)
    {
    	$this->_getResource()->setStockWebsiteId($stockWebsiteId);
    }
    
    
}