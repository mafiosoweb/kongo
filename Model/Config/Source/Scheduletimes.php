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
* Config source for attribute schedule times
* 
* @category Nostress 
* @package Nostress_Koongo
* 
*/
namespace Nostress\Koongo\Model\Config\Source;

class Scheduletimes  extends \Nostress\Koongo\Model\Config\Source
{		
	const EVERY_15M = "every_15m";
	const EVERY_30M = "every_30m";
	const EVERY_60M = "every_60m";
	const EVERY_2H = "every_2h";
	const EVERY_4H = "every_4h";
	const EVERY_6H = "every_6h";
	const EVERY_12H = "every_12h";
	const EVERY_24H = "every_24h";
	
    public function toOptionArray()
    {
        return array(    
        			array('value'=> self::EVERY_24H, 'label'=> __("Once per day")),
        			array('value'=> self::EVERY_15M, 'label'=> __("Every 15 minutes")),
				    array('value'=> self::EVERY_30M, 'label'=> __("Every 30 minutes")),
				    array('value'=> self::EVERY_60M, 'label'=> __("Every hour")),
        			array('value'=> self::EVERY_2H, 'label'=> __("Every 2 hours")),
        			array('value'=> self::EVERY_4H, 'label'=> __("Every 4 hours")),
        			array('value'=> self::EVERY_6H, 'label'=> __("Every 6 hours")),
        			array('value'=> self::EVERY_12H, 'label'=> __("Every 12 hours"))        			     	        	       	
        );
    }   
}
?>
