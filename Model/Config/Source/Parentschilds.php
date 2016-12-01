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
 * Config source model - parent child dependency
*
* @category Nostress
* @package Nostress_Koongo
*
*/
namespace Nostress\Koongo\Model\Config\Source;

class Parentschilds  extends \Nostress\Koongo\Model\Config\Source
{
	const PARENTS_AND_CHILDS = 0;
	const PARENTS_ONLY = 1;
	const CHILDS_ONLY = 2;
	
	public function toOptionArray()
	{
		return array(
				array('value'=> self::PARENTS_AND_CHILDS, 'label'=> __('Products and Variants')),
				array('value'=>self::PARENTS_ONLY, 'label'=> __('Products only (without Variants/Childs)')),
				array('value'=>self::CHILDS_ONLY, 'label'=> __('Variants only (without Parent Products)')),
		);
	}
}