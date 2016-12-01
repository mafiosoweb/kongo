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
* Config source for dropdown menu "Product group size"
* 
* @category Nostress 
* @package Nostress_Koongo
* 
*/
namespace Nostress\Koongo\Model\Config\Source;

class Productgroupsize  extends \Nostress\Koongo\Model\Config\Source
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'100', 'label'=> __('100')),
            array('value'=>'300', 'label'=> __('300')),
            array('value'=>'500', 'label'=> __('500')),  
            array('value'=>'1000', 'label'=> __('1000')),
            array('value'=>'2000', 'label'=> __('2000')),
            array('value'=>'5000', 'label'=> __('5000')),
            array('value'=>'10000', 'label'=> __('10000')), 
        	array('value'=>'20000', 'label'=> __('20000')),
        );
    }
}
?>
