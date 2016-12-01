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
namespace Nostress\Koongo\Block\Adminhtml\Category\Checkboxes;

/**
 * Categories tree with checkboxes
 *
 * @author     Nostress Team <info@nostresscommerce.c>
 */

use Magento\Framework\Data\Tree\Node;

class Tree extends \Magento\Catalog\Block\Adminhtml\Category\Checkboxes\Tree
{
	/**
	 * Tree representation as php array
	 * @var unknown_type
	 */
	protected $treeRootArray;
	
	/**
	 * Category info indexed array
	 * @var unknown_type
	 */
	protected $categoryIndexedArray;
	
    /**
     * @return void
     */
    protected function _prepareLayout()
    {
        $this->setTemplate('Nostress_Koongo::catalog/category/checkboxes/tree.phtml');
    }
    
    /**
     * @param mixed|null $parenNodeCategory
     * @return string
     */
    public function getTreeJson($parenNodeCategory = null)
    {    	
    	$rootArray = $this->getTreeRootArray($parenNodeCategory);
    	$json = $this->_jsonEncoder->encode(isset($rootArray['children']) ? $rootArray['children'] : []);
    	return $json;
    }
    
    protected function getTreeRootArray($parenNodeCategory = null)
    {
    	if(!isset($this->treeRootArray))
    		$this->treeRootArray = $this->_getNodeJson($this->getRoot($parenNodeCategory));
    	return $this->treeRootArray;    		
    }
    
    public function getCategoriesInfoJson()
    {
    	$this->categoryIndexedArray = [];
    	$node = $this->getTreeRootArray();
    	if(!empty($node['children']))
    	{
    		//Skip default category if it is just one
    		if(count($node['children']) == 1)
    		{    			
    			$node = $node['children'][0];
    			//Save Default category
    			$name = htmlspecialchars_decode($node['name']);
    			$this->categoryIndexedArray[(int)$node['id']] = ['name' => $name,'path' => $name];
    		}
    			
    		if(!empty($node['children']))
    		{    		
    			foreach($node['children'] as $childItem)
    			{
    				$this->populateCategoriesIndexedArray("",$childItem);    	
    			}
    		}
    	}
    	return json_encode($this->categoryIndexedArray);

    	
    }
    
    protected function populateCategoriesIndexedArray($parentPath = null,$item)
    {    	    	 
    	$name = $path = htmlspecialchars_decode($item['name']);
    	
    	if(!empty($parentPath))
    	{
    		$path = $parentPath.' > '.$path;
    	}
  	    	    	
    	$this->categoryIndexedArray[(int)$item['id']] = ['name' => $name,'path' => $path];
    	
    	if(!empty($item['children']))
    	{
    		foreach($item['children'] as $childItem)
    		{
    			$this->populateCategoriesIndexedArray($path,$childItem);    			
    		}
    	}
    }
    
    /**
     * @param array|Node $node
     * @param int $level
     * @return array
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _getNodeJson($node, $level = 1)
    {
    	$item = parent::_getNodeJson($node, $level);
    	$item['name'] = $this->escapeHtml($node->getName());
    	return $item;
    }
}
