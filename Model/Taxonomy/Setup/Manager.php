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
* Class for taxonomy setup updates
*
* @category Nostress
* @package Nostress_Koongo
*
*/

namespace Nostress\Koongo\Model\Taxonomy\Setup;

class Manager  extends \Magento\Framework\Model\AbstractModel 
{	
	/**
	 * @var \Nostress\Koongo\Model\Taxonomy\SetupFactory
	 */
	protected $taxonomySetupFactory;
	
	/**
	 * Construct
	 *
	 * @param \Nostress\Koongo\Model\Taxonomy\SetupFactory $taxonomySetupFactory	
	 */
	public function __construct(\Nostress\Koongo\Model\Taxonomy\SetupFactory $taxonomySetupFactory)
	{
		$this->taxonomySetupFactory = $taxonomySetupFactory;
	}
	
	public function updateTaxonomySetup($data)
    {
    	$data = $this->prepareData($data);    	
    	$this->updateData($data);    	    	
    }
    
    protected function updateData($data)
    {
    	$collection = $this->taxonomySetupFactory->create()->getCollection()->load();
    	foreach($collection as $item)
    	{
    		$code = $item->getCode();
    		if(isset($data[$code]))
    		{
    			$this->copyData($data[$code],$item);
    			unset($data[$code]);
    		}
    		else 
    		{
    			$item->delete();
    		}
    	}    	
    	$this->insertData($data,$collection);
    	$collection->save();
    }
    
    protected function insertData($data,$collection)
    {
        foreach($data as $itemData)
    	{
    		$colItem = $collection->getNewEmptyItem();
    		$colItem->setData($itemData);    		
    		$collection->addItem($colItem);
    	}    	
    }
    
    protected function copyData($data,$dstItem)
    {
    	foreach($data as $key => $src)
    	{
    		$dstItem->setData($key,$src);
    	}
    }
    
    protected function prepareData($data)
    {
    	$modifData = array();
    	foreach($data as $key => $item)
    	{
    		if(!isset($item[\Nostress\Koongo\Model\Taxonomy\Setup::COL_CODE]))
    		{	
    			throw new \Exception(__("Missing taxonomy setup attribute '".\Nostress\Koongo\Model\Taxonomy\Setup::COL_CODE."'"));
    		}
    		$modifData[$item[\Nostress\Koongo\Model\Taxonomy\Setup::COL_CODE]] = $item;
    		
    	}
    	return $modifData;
    }			
}