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
* Config source for multiselect menu "Post Process Action"
* 
* @category Nostress 
* @package Nostress_Koongo
* 
*/
namespace Nostress\Koongo\Model\Config\Source;

class Postprocactions  extends \Nostress\Koongo\Model\Config\Source
{
	const PP_CDATA = 'cdata';
	const PP_ENCODE_SPECIAL = 'encode_special_chars';
	const PP_DECODE_SPECIAL = 'decode_special_chars';
	const PP_ENCODE_HTML_ENTITY = 'encode_html_entity';
	const PP_DECODE_HTML_ENTITY = 'decode_html_entity';
	const PP_REMOVE_EOL = 'remove_eol';
	const PP_STRIP_TAGS = 'strip_tags';
	const PP_DELETE_SPACES = 'delete_spaces';
	
    public function toOptionArray()
    {
        return array(        		
        	array('value'=>self::PP_CDATA, 'label'=> __("Encalpsulate into <![CDATA[]]>")),
        	array('value'=>self::PP_ENCODE_HTML_ENTITY, 'label'=> __("Convert Text to HTML")),        	
        	array('value'=>self::PP_ENCODE_SPECIAL, 'label'=> __("Convert Text to HTML - only: &, \", ', <, >")),
        	array('value'=>self::PP_DECODE_HTML_ENTITY, 'label'=> __("Convert HTML to Text")),
        	array('value'=>self::PP_DECODE_SPECIAL, 'label'=> __("Convert HTML to Text - only: &amp;, &quot;, &#039;, &lt, &gt;")),                          
            array('value'=>self::PP_REMOVE_EOL, 'label'=> __("Remove End of Line Characters")),
            array('value'=>self::PP_STRIP_TAGS, 'label'=> __("Remove HTML Tags/Elements")),
            array('value'=>self::PP_DELETE_SPACES, 'label'=> __("Remove Spaces"))        	
        );
    }
}
?>
