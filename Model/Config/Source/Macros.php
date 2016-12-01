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
* Config source for attribute composition Macros
* 
* @category Nostress 
* @package Nostress_Koongo
* 
*/
namespace Nostress\Koongo\Model\Config\Source;

class Macros  extends \Nostress\Koongo\Model\Config\Source
{	
    public function toOptionArray()
    {
        return array(       
        		array('value'=> '{{name}} - {{sku}} - {{color}} - Large', 'label'=> __("Attribute Merge")),
        	array('value'=> '[[round({{nkp_price_final_include_tax}} * 1.15, 2)]]', 'label'=> __("Increase Price by 15%")),
        	array('value'=> '[[round({{nkp_price_final_include_tax}} + 25, 2)]]', 'label'=> __("Increase Price by 25")),
        	array('value'=> '[[ +-*/ ]]', 'label'=> __("Empty Math Operation")),        	
        	array('value'=> "[[('{{meta_title}}' != '')? '{{meta_title}}': '{{name}}';]]"        			
        			, 'label'=> __("Empty Attribute Condition")),
        	array('value'=> "[[ ( {{nkp_price_final_include_tax}} > 100 ) ?  {{nkp_price_final_include_tax}}: {{nkp_price_final_include_tax}} + 20;]]"
        				, 'label'=> __("Attribute Value Condition"))
        	       	
        );
    }
}
?>
