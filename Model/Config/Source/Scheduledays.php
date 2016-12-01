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
* @copyright Copyright (c) 2009 NoStress Commerce (http://www.nostresscommerce.cz) 
* 
*/ 

/** 
* Config source for schedule days
* 
* @category Nostress 
* @package Nostress_Koongo
* 
*/
namespace Nostress\Koongo\Model\Config\Source;

class Scheduledays  extends \Nostress\Koongo\Model\Config\Source
{	
	const EVERY_DAY = "everyday";
	const EVERY_WORKDAY = "everyworkday";
	const EVERY_WEEKENDDAY = "everyweekendday";
	const MONDAY = 1;	
	const TUESDAY = 2;
	const WEDNESDAY = 3;
	const THURSDAY = 4;
	const FRIDAY = 5;
	const SATURDAY = 6;
	const SUNDAY = 7;
	
    public function toOptionArray()
    {
        return array(
        			array('value'=> self::EVERY_DAY, 'label'=> __("Daily")),
        			array('value'=> self::EVERY_WORKDAY, 'label'=> __("Every workday")),
        			array('value'=> self::EVERY_WEEKENDDAY, 'label'=> __("Every day on weekend")),
	        		array('value'=> self::MONDAY, 'label'=> __("Every Monday")),
	        		array('value'=> self::TUESDAY, 'label'=> __("Every Tuesday")),
	        		array('value'=> self::WEDNESDAY, 'label'=> __("Every Wednesday")),
	        		array('value'=> self::THURSDAY, 'label'=> __("Every Thursday")),
	        		array('value'=> self::FRIDAY, 'label'=> __("Every Friday")),
	        		array('value'=> self::SATURDAY, 'label'=> __("Every Saturday")),
	        		array('value'=> self::SUNDAY, 'label'=> __("Every Sunday"))
    	);        		
    }   
}
?>
