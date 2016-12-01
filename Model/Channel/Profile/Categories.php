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
* Class for profile processing.
*
* @category Nostress
* @package Nostress_Koongo
*
*/

namespace Nostress\Koongo\Model\Channel\Profile;

use Nostress\Koongo\Model\Channel\Profile;

class Categories  extends \Nostress\Koongo\Model\AbstractModel
{
    /**
     *  @var \Nostress\Koongo\Model\Channel\Profile
     **/
    protected $profile;
    
    /**
     * Channel category source
     * @var unknown_type
     */
    protected $categorySource;
    
    /**
     *
     * @var \Nostress\Koongo\Model\Api\Client
     */
    protected  $client;
    /**
     *
     * @var \Nostress\Koongo\Helper\Data
     */
    public $helper;
    
    /**
     *
     * @var \Nostress\Koongo\Model\Taxonomy\Setup
     */
    protected $taxonomyModel;
    
    /**
     * @var \Nostress\Koongo\Model\Taxonomy\Category\Mapping
     */
    protected $mappingModel;
    
    /**
     * @param \Nostress\Koongo\Model\Taxonomy\Category $categorySource
     * @param \Nostress\Koongo\Helper\Data $helper
     * @param \Nostress\Koongo\Model\Api\Client $client
     * @param \Nostress\Koongo\Model\Taxonomy\Category\Mapping $mappingModel
     * @param array $data
     */
    public function __construct(
    		\Nostress\Koongo\Model\Taxonomy\Category $categorySource,
    		\Nostress\Koongo\Helper\Data $helper,
    		\Nostress\Koongo\Model\Api\Client $client,
    		\Nostress\Koongo\Model\Taxonomy\Category\Mapping $mappingModel
    ) {
    	$this->categorySource = $categorySource;
    	$this->client = $client;
    	$this->helper = $helper;
    	$this->mappingModel = $mappingModel;
    }
    
    public function initProfile( $profile) {
        
        $this->profile = $profile;
        $this->taxonomyModel = $this->profile->getFeed()->getTaxonomy();
    }

    /**
     * after 'Update Channel Category Tree' button is clicked
     * @param string $locale
     */
    public function reloadTaxonomyCategories( $locale = null) {
        
        if( $locale === null) {
            $locale = $this->getCurrentLocale();
        }
        $taxonomyCode = $this->taxonomyModel->getCode();
        return $this->client->reloadTaxonomyCategories($taxonomyCode,$locale);
    }
    
    public function getMappingRules( $locale = null)
    {
        if( $locale === null) {
    	   $locale = $this->getCurrentLocale();
        }
    	
    	$mappingItem = $this->mappingModel->getMapping($this->taxonomyModel->getCode(),$locale, $this->profile->getStoreId());
    	if(isset($mappingItem))
    	{
    		return $mappingItem->getRules();
    	} else {
    	    return array();
    	}
    }
    
    public function getChannelCategories( $locale = null)
    {
        if( $locale === null) {
    	   $locale = $this->getCurrentLocale();
        }
		$taxonomyCode = $this->taxonomyModel->getCode();
    	
    	$columnsCount = $this->categorySource->countColumns($taxonomyCode,$locale);
    	if($columnsCount <= 0)
    	{
    		$this->client->reloadTaxonomyCategories($taxonomyCode,$locale);
    	}
    	
    	$categories = $this->categorySource->getCategories($taxonomyCode,$locale,$this->taxonomyModel->getDefaultLocale(),['name','path','hash','id','parent_id'],'hash');
    	
    	// format categories
    	$catPathDelimiter = $this->taxonomyModel->getCategoryPathDelimiter();
    	foreach($categories as $key => &$item)
    	{
    	    $item['name_folded'] = strtolower( $this->helper->removeAccent( $item['name']));
    	    	
    	    if(isset($item['path']))
    	    {
    	        $pathArray = explode($catPathDelimiter, $item['path']);
    	        $item['pathitems'] = $pathArray;
    	        $item['path_folded'] = strtolower( $this->helper->removeAccent( $item['path']));
    	    }
    	}
    	unset( $item);
    	
    	return $categories;
    }
    
    public function getCurrentLocale()
    {
    	$storeLocale = $this->helper->getStoreConfig($this->profile->getStoreId(),\Nostress\Koongo\Helper\Data::PATH_STORE_LOCALE);
    	$availabelLocales = $this->taxonomyModel->getAvailableLocales();
    	 
    	//Load current locale - load from profile -> load from store -> set default
    	$currentLocale = $this->profile->getConfigItem(Profile::CONFIG_GENERAL,false,'taxonomy_locale');
    	if(empty($currentLocale) && in_array($storeLocale, $availabelLocales))
    	{
    		$currentLocale = $storeLocale;
    	}
    	if(empty($currentLocale))
    		$currentLocale = $this->taxonomyModel->getDefaultLocale();
    	
    	return $currentLocale;
    }
}