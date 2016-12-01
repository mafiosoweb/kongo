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
* Config source model - Column delimiter
* 
* @category Nostress 
* @package Nostress_Koongo
* 
*/
namespace Nostress\Koongo\Model\Config\Source;

class Columndelimiter  extends \Nostress\Koongo\Model\Config\Source
{        
    public function toOptionArray()
    {
    	$options = [];
        $values = $this->getOptions();
        foreach ($values as $index => $label)
        {
        	$options[] = ['value' => $index, 'label' => $label];
        }
        return $options;
    }
    
    public function toIndexedArray()
    {
    	return $this->getOptions();
    }
    
	protected function getOptions() 
	{		
		$options = array("|" => __("Pipe")." - ( | )",
							"," => __("Comma")." - ( , )",
							"\t" => __("Tab")." - ( \\t )",
							" " => __("Space")." - ( ' ' )",
							";" => __("Semicolon")." - ( ; )",
							"/" => __("Slash")." - ( / )",
							"-" => __("Dash")." - ( - )",
							"*" => __("Star")." - ( * )",
							'\\' => __("Backslash")." - ( \\ )",
							":" => __("Colon")." - ( : )",
							"#" => __("Grid")." - ( # )",
							"&" => __("Ampersand")." - ( & )",
							"~" => __("Tilde")." - ( ~ )"
							);
		
		return $options;
	}
}
?>