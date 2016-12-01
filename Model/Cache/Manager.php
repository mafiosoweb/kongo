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
 * Manager for Koongo connector cache models
 *
 * @category Nostress
 * @package Nostress_Koongo
 *
 */

namespace Nostress\Koongo\Model\Cache;

use Nostress\Koongo\Model\Channel\Profile;

class Manager  extends \Nostress\Koongo\Model\AbstractModel
{
	/*
	 * @var \Nostress\Koongo\Model\Cache\Weee 
	 */
	protected $cacheWeee;
	
	/*
	 * @var \Nostress\Koongo\Model\Cache\Tax
	 */
	protected $cacheTax;
	
	/*
	 * @var \Nostress\Koongo\Model\Cache\Categorypath
 	*/
	protected $cacheCategorypath;
	
	/*
	 * @var \Nostress\Koongo\Model\Cache\Product
	  */
	protected $cacheProduct;
	
	/*
	* @var \Nostress\Koongo\Model\Cache\Profilecategory
	*/	
	protected $cacheProfileCategory;
	
	/*
	 * @var \Nostress\Koongo\Model\Cache\Channelcategory
	*/
	protected $cacheChannelCategory;
	
	/**
	 * \Nostress\Koongo\Model\Taxonomy\Category\Mapping
	 */
	protected $mappingModel;
	
	/**
	 * @param \Nostress\Koongo\Helper\Data\Loader $helper
	 * @param \Magento\Store\Model\StoreManagerInterface $storeManager
	 * @param \Nostress\Koongo\Model\Cache\Weee $cacheWeee
	 * @param \Nostress\Koongo\Model\Cache\Tax $cacheTax
	 * @param \Nostress\Koongo\Model\Cache\Categorypath $cacheCategorypath
	 * @param \Nostress\Koongo\Model\Cache\Product $cacheProduct
	 */
	public function __construct(
			\Nostress\Koongo\Helper\Data\Loader $helper,
			\Magento\Store\Model\StoreManagerInterface $storeManager,
			\Nostress\Koongo\Model\Cache\Weee $cacheWeee,
			\Nostress\Koongo\Model\Cache\Tax $cacheTax,
			\Nostress\Koongo\Model\Cache\Categorypath $cacheCategorypath,
			\Nostress\Koongo\Model\Cache\Product $cacheProduct,
			\Nostress\Koongo\Model\Cache\Profilecategory $cacheProfileCategory,
			\Nostress\Koongo\Model\Cache\Channelcategory $cacheChannelCategory,
			\Nostress\Koongo\Model\Taxonomy\Category\Mapping $mappingModel
	)
	{
		$this->helper = $helper;
		$this->storeManager = $storeManager;
		$this->cacheWeee = $cacheWeee;
		$this->cacheTax = $cacheTax;
		$this->cacheCategorypath = $cacheCategorypath;
		$this->cacheProduct = $cacheProduct;
		$this->cacheProfileCategory = $cacheProfileCategory;
		$this->cacheChannelCategory = $cacheChannelCategory;
		$this->mappingModel = $mappingModel;
	}	
	
	public function reloadAllCache($storeIds, $websiteIds)
	{
		foreach ($storeIds as $storeId)
		{
			$categoryLowestLevel = $this->helper->getModuleConfig(\Nostress\Koongo\Helper\Data::PARAM_CATEGORY_LOWEST_LEVEL,false,false,$storeId);
			
			//Reload category path
			$this->cacheCategorypath->setLowestLevel($categoryLowestLevel); 
			$this->cacheCategorypath->reload($storeId);
			
			
			//Load lowest level from module configuration(store dependent)								
			$this->cacheProduct->setLowestLevel($categoryLowestLevel); 
			
			//Load excluded images export status from module configuration(store dependent)
			$excludedImagesExportEnabled = $this->helper->getModuleConfig(\Nostress\Koongo\Helper\Data::PARAM_ALLOW_EXCLUDED_IMAGES_EXPORT,false,false,$storeId);
			$this->cacheProduct->setExcludedImagesExportEnabled($excludedImagesExportEnabled);
			
			//Load inactive categories export status from module configuration(store dependent)
			$allowInactiveCategoriesExport = $this->helper->getModuleConfig(\Nostress\Koongo\Helper\Data::PARAM_ALLOW_INACTIVE_CATEGORIES_EXPORT,false,false,$storeId);
			$this->cacheProduct->setAllowInactiveCategoriesExport($allowInactiveCategoriesExport);

			//Load website id for stock status table
			$stockWebsiteId = $this->helper->getStockWebsiteId();
			$this->cacheProduct->setStockWebsiteId($stockWebsiteId);
			
			$this->cacheProduct->reload($storeId);
			$this->cacheTax->reload($storeId);						
		}
		
		foreach ($websiteIds as $websiteId) {
			$this->cacheWeee->reload($websiteId);
		}
	}
	
	public function reloadProfileCache($profile)
	{
		$categoryIds = $profile->getConfigItem(Profile::CONFIG_FILTER,false,Profile::CONFIG_FILTER_CATEGORIES);		
		
		//Reload profile categories cache
		if(!empty($categoryIds))
		{		
			$storeId = $profile->getStoreId();
			$categoryLowestLevel = $this->helper->getModuleConfig(\Nostress\Koongo\Helper\Data::PARAM_CATEGORY_LOWEST_LEVEL,false,false,$storeId);
			$allowInactiveCategoriesExport = $this->helper->getModuleConfig(\Nostress\Koongo\Helper\Data::PARAM_ALLOW_INACTIVE_CATEGORIES_EXPORT,false,false,$storeId);
			$this->cacheProfileCategory->setParameters($profile->getId(), $categoryIds, $categoryLowestLevel, $allowInactiveCategoriesExport);
			$this->cacheProfileCategory->reload($storeId);
		}				
				
		//Reload channel categories cache
		$this->reloadProfileChannelCategoriesCache($profile);					
	}	
	
	public function reloadProfileChannelCategoriesCache($profile)
	{		
		$taxonomyCode = $profile->getFeed()->getTaxonomyCode();
		$locale = $profile->getConfigItem(Profile::CONFIG_GENERAL,false,'taxonomy_locale');		
		
		//Reload channel categories cache
		$this->reloadChannelCategoriesCache($profile->getId(),$taxonomyCode,$locale,$profile->getStoreId());
	}
	
	protected function reloadChannelCategoriesCache($profileId,$taxonomyCode,$locale,$storeId)
	{
		//Reload channel categories cache
		if(!empty($taxonomyCode) && !empty($locale))
		{
			$mappingModel = $this->mappingModel->getMapping($taxonomyCode,$locale, $storeId);
			$rules = [];
			if(isset($mappingModel))
				$rules = $mappingModel->getRules();
				
			$this->cacheChannelCategory->setParameters($profileId, $taxonomyCode, $locale, $rules);
			$this->cacheChannelCategory->reload($storeId);
			
		}
	}
	
}