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
* Config source for dropdown menu "Category Lowest Level"
* 
* @category Nostress 
* @package Nostress_Koongo
* 
*/
namespace Nostress\Koongo\Model\Config\Source;

class Categorylevel  extends \Nostress\Koongo\Model\Config\Source
{
    public function toOptionArray()
    {    	
        return array(
            array('value'=>'1', 'label'=> '1'),
            array('value'=>'2', 'label'=> '2'),
            array('value'=>'3', 'label'=> '3'),  
            array('value'=>'4', 'label'=> '4'),
            array('value'=>'5', 'label'=> '5'),
            array('value'=>'6', 'label'=> '6'),
            array('value'=>'7', 'label'=> '7'), 
        	array('value'=>'8', 'label'=> '8'),
        	array('value'=>'9', 'label'=> '9'),
        	array('value'=>'10', 'label'=> '10'),
        );
    }
}
?>
