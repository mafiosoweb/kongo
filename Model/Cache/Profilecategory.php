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

class Profilecategory  extends \Nostress\Koongo\Model\Cache 
{    	    
    public function _construct()
	{    	
    	$this->_init('Nostress\Koongo\Model\ResourceModel\Cache\Profilecategory');
    }
    
    public function setParameters($profileId, $categoryIds, $lowestLevel, $allowInactiveCategoriesExport)
    {
    	$resource = $this->_getResource();
    	
    	$resource->setLowestLevel($lowestLevel);
    	$resource->setAllowInactiveCategoriesExport($allowInactiveCategoriesExport);
    	$resource->setProfileId($profileId);
    	$resource->setCategoryIds($categoryIds);
    }        
}