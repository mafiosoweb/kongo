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
* Manager for export profiles
*
* @category Nostress
* @package Nostress_Koongo
*
*/

namespace Nostress\Koongo\Model\Channel\Profile;

use Nostress\Koongo\Model\Channel\Profile;

class Manager  extends \Nostress\Koongo\Model\AbstractModel
{
	protected $_errorList;
	
	/*
	 * @var Nostress\Koongo\Model\Channel\ProfileFactory
	 */
	protected $profileFactory;
	
	/*
	 * @var \Nostress\Koongo\Model\Channel\Profile\Processor
	 */
	protected $profileProcessor;
	
	/*
	 * @var \Nostress\Koongo\Model\Channel\Profile\Ftp
	 */
	protected $ftp;
	
	/*
	 * @var \Nostress\Koongo\Model\Cache\Manager
	 */
	protected $cacheManager;
	
	/*
	 * @var \Nostress\Koongo\Model\Channel\FeedFactory
	 */
	protected $feedFactory;
	
	/*
	 * @var \Nostress\Koongo\Model\Config\Source\Attributes
	*/
	protected $attributeSource;
	
	/**
	 * Export profile cron execution
	 * \Nostress\Koongo\Model\Cron $cron
	 */
	protected $cron;
	
	/**
	 * @var \Nostress\Koongo\Helper\Version
	 */
	protected $version;

	/**
	 *
	 * @param \Nostress\Koongo\Helper\Data\Loader $helper
	 * @param \Magento\Store\Model\StoreManagerInterface $storeManager
	 * @param \Nostress\Koongo\Model\Channel\ProfileFactory $profileFactory
	 * @param \Nostress\Koongo\Model\Channel\Profile\Processor $profileProcessor
	 * @param \Nostress\Koongo\Model\Channel\Profile\Ftp $ftp
	 * @param \Nostress\Koongo\Model\Cache\Manager $cacheManager
	 * @param \Nostress\Koongo\Model\Channel\FeedFactory $feedFactory
	 * @param \Nostress\Koongo\Model\Config\Source\Attributes $attributeSource
	 * @param \Nostress\Koongo\Model\Translation $translation
	 * @param \Nostress\Koongo\Model\Cron $cron
	 * @param \Nostress\Koongo\Helper\Version $version
	 */
	public function __construct(
			\Nostress\Koongo\Helper\Data\Loader $helper,
			\Magento\Store\Model\StoreManagerInterface $storeManager,
			\Nostress\Koongo\Model\Channel\ProfileFactory $profileFactory,
			\Nostress\Koongo\Model\Channel\Profile\Processor $profileProcessor,
			\Nostress\Koongo\Model\Channel\Profile\Ftp $ftp,
			\Nostress\Koongo\Model\Cache\Manager $cacheManager,
			\Nostress\Koongo\Model\Channel\FeedFactory $feedFactory,
			\Nostress\Koongo\Model\Config\Source\Attributes $attributeSource,
			\Nostress\Koongo\Model\Translation $translation,
			\Nostress\Koongo\Model\Cron $cron,
	        \Nostress\Koongo\Helper\Version $version
	)
	{
		$this->helper = $helper;
    	$this->storeManager = $storeManager;
    	$this->profileFactory = $profileFactory;
    	$this->profileProcessor = $profileProcessor;
    	$this->ftp = $ftp;
    	$this->cacheManager = $cacheManager;
    	$this->feedFactory = $feedFactory;
    	$this->attributeSource = $attributeSource;
    	$this->translation = $translation;
    	$this->cron = $cron;
    	$this->version = $version;
	}

    public function updateProfilesFeedConfig()
    {
        $profiles = $this->getAllProfiles();

        foreach ($profiles as $profile)
        {
            $this->updateProfileFeedConfig($profile);
        }
    }
    /**************************** Profile execute functions **************************/
    public function runAllProfiles()
    {
    	$profiles = $this->getAllProfiles();
    	$this->runProfiles($profiles);
    	return $profiles;
    }
    
    public function runProfilesWithErrorStatus()
    {
    	$profiles = $this->getProfilesWithErrorStatus();    	    	
    	$this->runProfiles($profiles);
    	return $profiles;
    }    
    
    public function runProfilesByCron()
    {
    	$profileIds = $this->cron->getScheduledProfileIds();
    	$profiles = $this->runProfilesByIds($profileIds,true);
    	return $profiles;
    }
    
    public function runProfilesByIds($profileIds, $upload = false)
    {
    	$profiles = $this->getProfilesByIds($profileIds);
    	$this->runProfiles($profiles, $upload);
    	return $profiles;
    }
    
    public function runProfilesByNames($profileNames, $upload = false)
    {
    	$profiles =$this->getProfilesByNames($profileNames);
    	$this->runProfiles($profiles, $upload);
    	return $profiles;
    }
    
    public function runProfiles($profiles, $upload = false)
    {
    	if(empty($profiles))
    		return;
    	    	
    	if(! $this->version->isLicenseValid()) {
    	    return false;
    	}
    	
    	if(!$this->checkFlatTablesExist($profiles))
    		return;
    	
    	$this->reloadCache($profiles);
    	
    	foreach ($profiles as $item)
    	{
    		try
    		{
    			$this->runProfile($item, $upload);
    			
    		}
    		catch(\Exception $e)
    		{
    			$this->log($e->getMessage());
    		}
    	}
    	
    	return $profiles;
    }
    
    protected function runProfile($profile, $upload = false)
    {
    	if(!$this->checkProfileAttributes($profile))
    	{
    		return;
    	}
    	
    	$this->reloadProfileCache($profile);
    	$this->profileProcessor->run($profile);
    	
    	if( $upload)
    		$this->ftp->uploadFeed($profile, true);
    }
    
    public function reloadProfileCache( $profile) {
        $this->cacheManager->reloadProfileCache($profile);
    }
    
    /**************************** Profile Attributes Check ***************************/
    /*
     * Check magento attributes if available in flat catalog
     */
    public function checkProfileAttributes($profile)
    {
    	try
    	{
    		$attributes = $profile->getMagentoAttributes();
    		$attributesMissingInProductFlat = $this->attributeSource->checkAttributesProductFlat($attributes,$profile->getStoreId());
    		if(!empty($attributesMissingInProductFlat))
    		{
    			$this->logAndException("7 ". implode(", ",$attributesMissingInProductFlat));
    		}
    	}
    	catch(\Exception $e)
        {
        	$message = $this->translation->processException($e);
        	$profile->setMessageStatusError($message,\Nostress\Koongo\Model\Channel\Profile::STATUS_ERROR);
            return false;
        }
    	return true;
    }
    
    public function addProfilesAttributesToFlat()
    {
    	$profiles = $this->getAllProfiles();
    	$attributes = [];
    	$ids = [];
    	foreach($profiles as $profile)
    	{
    		$profileAttributes = $profile->getMagentoAttributes();
    		$attributesMissingInProductFlat = $this->attributeSource->checkAttributesProductFlat($profileAttributes,$profile->getStoreId());
    		if(!empty($attributesMissingInProductFlat))
    			$ids[] = $profile->getId();
    		$attributes = array_merge($attributes,$attributesMissingInProductFlat);
    	}
    	$attributes = array_unique($attributes);
     	$this->helper->setEavAttributesPropertyValue($attributes, 'used_in_product_listing',"1");
    	return ["attributes" => $attributes, "profile_ids" => $ids];
    }
    
    /**************************** Profile load functions *****************************/
    protected function getAllProfiles()
    {
    	$collection = $this->profileFactory->create()->getCollection();
    	$collection->load();
    	return $collection->getItems();
    }
    
    protected function getProfilesWithErrorStatus()
    {
    	$collection = $this->profileFactory->create()->getCollection();
    	$select = $collection->getSelect();
    	$select->where('status',\Nostress\Koongo\Model\Channel\Profile::STATUS_ERROR);
    	return $collection->load();
    }
    
    protected function getProfilesByIds($profileIds)
    {
    	$collection = $this->profileFactory->create()->getCollection();
    	$select = $collection->getSelect();
    	$select->where('entity_id IN (?)',$profileIds);
    	return $collection->load();
    }
    
    protected function getProfilesByNames($names)
    {
    	$collection = $this->profileFactory->create()->getCollection();
    	$select = $collection->getSelect();
    	$select->where('name IN (?)',$names);
    	return $collection->load();
    }
    
    public function getStoreIdsByProfileIds($profileIds)
    {
    	$collection = $this->profileFactory->create()->getCollection();
    	$select = $collection->getSelect();
    	$select->columns('store_id');
    	$select->group('store_id');
    	$select->where('entity_id IN (?)',$profileIds);
    	$collection->load();
    	$storeIds = array();
    	foreach ($collection as $item)
    		$storeIds[] = $item->getStoreId();
    	return $storeIds;
    }        
    
    /***************************** Cache reload functions ********************************************/
    protected function checkFlatTablesExist($profiles)
    {
    	$resource = $profiles->getResource();    	
    	$storeIdsWhereFlatTablesExist = [];
    	$canRun = true; 
    	foreach($profiles as $profile)
    	{
    		$storeId =  $profile->getStoreId();
    		//Check if current store id has been already checked for existing tables
    		if(in_array($storeId, $storeIdsWhereFlatTablesExist))
    			continue;
    		
    		$productFlatTableName = $resource->getTable("catalog_product_flat_".$storeId);
    		$categoryFlatTableName = $resource->getTable("catalog_category_flat_store_".$storeId);
    		
    		$productFlatExists = $resource->getConnection()->isTableExists($productFlatTableName);
    		$categoryFlatExists = $resource->getConnection()->isTableExists($categoryFlatTableName);
    		
    		if($productFlatExists && $categoryFlatExists)
    			$storeIdsWhereFlatTablesExist[] = $storeId;
    		else
    		{
    			$canRun = false;
    			$message = $this->translation->processException("12");
    			$profile->setMessageStatusError($message,\Nostress\Koongo\Model\Channel\Profile::STATUS_ERROR);
    		}    		
    	}
    	
    	if($canRun)
    		return true;
    	else
    		return false;
    }
    
    protected function reloadCache($profiles)
    {
    	$ids = $this->getStoreWebsiteIdsFromProfiles($profiles);
    	if(!empty($ids))
    	{
    		$this->cacheManager->reloadAllCache($ids['store_ids'], $ids['website_ids']);
    	}
    	
    }
    protected function getStoreWebsiteIdsFromProfiles($profiles)
    {
    	if(empty($profiles))
    		return array();
    	$storeIds = array();
    	$websiteIds = array();
    	$ids = array();
    	foreach ($profiles as $profile)
    	{
    		if(is_numeric($profile))
    			$ids[] = $profile;
    		else
    		{
    			$storeId = $profile->getStoreId();
    			if(!in_array($storeId,$storeIds))
    				$storeIds[] = $storeId;
    		}
    	}
    	
    	if(!empty($ids))
    		$storeIds = $this->getStoreIdsByProfileIds($ids);
    	
    	foreach( $this->storeManager->getWebsites() as $website)
    	{
    		$websiteStoreIds = $website->getStoreIds();
    		$intersec = array_intersect($websiteStoreIds,$storeIds);
    		if(!empty($intersec))
    			$websiteIds[] = $website->getWebsiteId();
    	}
    	
    	return array("store_ids" => $storeIds, "website_ids" => $websiteIds);
    }
    /****************************** Profile management functions ****************************************/
    
    public function duplicateProfile($profileId)
    {
    	$profile = $this->profileFactory->create()->load($profileId);
    	$newProfile = $this->profileFactory->create();
    	$newProfile->setData($profile->getData());
    	$newProfile->defineFilename();
    	$newProfile->setId(null);
    	$newProfile->save();
    	return $newProfile;
    }
    
    public function createProfileFromFeed($storeId, $feedCode)
    {
    	$profile = $this->profileFactory->create();
    	$profile->setFeedCode($feedCode);
    	$profile->setStoreId($storeId);
       	
    	$feed = $this->feedFactory->create()->getFeedByCode($feedCode);
    	$profile->setFeed($feed);
    	$profile->setName($feed->getLink());
    	$profile->defineFilename();
    	$config = $this->prepareProfileConfig($profile, $feed);
    	$profile->setConfig($config);
    	$profile->save();
        
    	return $profile;
    }
    
    /**
     * Transforma profile from magento 1 connector to magento 2 connector
     * @param unknown_type $storeId
     * @param unknown_type $feedCode
     * @return unknown
     */
    public function transformProfile($profileData)
    {
    	$transferedFields = array("store_id","filename","name","status","message");
    	
    	$newProfile = $this->profileFactory->create();
    	foreach ($transferedFields as $field)
    	{
    		$newProfile->setData($field,$profileData[$field]);
    	}
    	
    	$feed = $this->feedFactory->create()->getFeedByCode($profileData['feed']);
    	$newProfile->setFeedCode($profileData['feed']);
    	$config = $this->transformConfig($profileData['config'], $feed->getAttributesSetup());
    	$newProfile->setConfig($config);
    	$newProfile->resetUrl();
    	$newProfile->save();
    
    	return $newProfile;
    }
    
    /**
     * Prepares profile config from feed config.
     * @param unknown_type $profile
     * @param unknown_type $feed
     * @return multitype:string
     */
    protected function prepareProfileConfig($profile,$feed)
    {
    	$setupArray = $feed->getAttributesSetup();
    	
    	$config = $profile->getDefaultConfig();
    	   
    	//common setup
    	foreach ($setupArray[Profile::CONFIG_COMMON] as $key => $data)
    	{
    		if($key == Profile::CONFIG_CUSTOM_PARAMS && is_array($data) && array_key_exists(Profile::CONFIG_PARAM,$data) && is_array($data[Profile::CONFIG_PARAM]))
    		{
    			$customParams = array();
    			$customParamsSrcArray = $data[Profile::CONFIG_PARAM];
    			if(isset($customParamsSrcArray[Profile::CONFIG_CODE]))
    				$customParamsSrcArray = array($customParamsSrcArray);
    			 
    			foreach ($customParamsSrcArray as $paramData)
    			{
    				if(is_array($paramData) && array_key_exists(Profile::CONFIG_CODE, $paramData))
    				{
    					$value = "";
    					if(array_key_exists(Profile::CONFIG_VALUE, $paramData))
    						$value = $paramData[Profile::CONFIG_VALUE];
    					$customParams[$paramData[Profile::CONFIG_CODE]] = $value;
    				}
    			}
    			$config[Profile::CONFIG_FEED][Profile::CONFIG_COMMON][Profile::CONFIG_CUSTOM_PARAMS] = $customParams;
    		}
    		else
    			$config[Profile::CONFIG_FEED][Profile::CONFIG_COMMON][$key] = $data;
    	}

    	//currency, datetime format
    	if(empty($config[Profile::CONFIG_FEED][Profile::CONFIG_COMMON][Profile::CONFIG_DATETIME_FORMAT]))
    		$config[Profile::CONFIG_FEED][Profile::CONFIG_COMMON][Profile::CONFIG_DATETIME_FORMAT] = \Nostress\Koongo\Model\Config\Source\Datetimeformat::STANTDARD;
    	$config[Profile::CONFIG_FEED][Profile::CONFIG_COMMON][Profile::CONFIG_CURRENCY] = $this->storeManager->getStore($profile->getStoreId())->getBaseCurrencyCode();
    	
    	
    	//adjust taxonomy locale
    	if($feed->hasTaxonomy())
    	{
    		$taxonomyModel = $feed->getTaxonomy();
    		$storeLocale = $this->helper->getStoreConfig($profile->getStoreId(),\Nostress\Koongo\Helper\Data::PATH_STORE_LOCALE);
    		$availabelLocales = $taxonomyModel->getAvailableLocales();
    		
    		//Load from store -> set default
    		if(in_array($storeLocale, $availabelLocales))
    		{
    			$currentLocale = $storeLocale;
    		}
    		if(empty($currentLocale))
    			$currentLocale = $taxonomyModel->getDefaultLocale();
    		
    		$config[Profile::CONFIG_GENERAL][Profile::CONFIG_TAXONOMY_LOCALE] = $currentLocale;
    	}
    	
    	//attributes
    	$attributes = array();
    	$feedLayoutAttributes = $feed->getFeedAttributes();
    	 
    	foreach ($feedLayoutAttributes as $attibuteSetup)
    	{
    		$attribute = array();
    		if(!is_array($attibuteSetup))
    			continue;
    		
    		$attributes[] = $this->transformFeedAttributeSetupToProfile($attibuteSetup);
    	}
    	 
    	$config[Profile::CONFIG_FEED][Profile::CONFIG_ATTRIBUTES] = $attributes;
    	 
    	return $config;
    }
    
    /**
     * Transforms profile config from magento 1 connector to magento 2 connector format
     * @param unknown_type $profile
     * @param unknown_type $feed
     */
    protected function transformConfig($config,$feedAttributeSetup)
    {
    	$config = json_decode($config,true);
    	$newConfig = array();
    	$newConfig[Profile::CONFIG_FILTER] = array();
    	foreach ($config as $key => $data)
    	{
    		if($key == "product" || $key == "attribute_filter")
    		{
    			foreach($data as $index => $item)
    			{
    				if($index != "use_product_filter" && $index != "automatically_add_new_products_use_default")
    					$newConfig[Profile::CONFIG_FILTER][$index] = $item;
    			}
    		}
    		else
    			$newConfig[$key] = $data;
    	}
    	
    	//convert custom params
    	if(isset($newConfig[Profile::CONFIG_FEED][Profile::CONFIG_COMMON][Profile::CONFIG_CUSTOM_PARAMS][Profile::CONFIG_PARAM]))
    	{
    		$customParams = $newConfig[Profile::CONFIG_FEED][Profile::CONFIG_COMMON][Profile::CONFIG_CUSTOM_PARAMS][Profile::CONFIG_PARAM];
    		$newCustomParams = array();
    		$i = 0;
    		
    		foreach($customParams as $index => $item)
    		{
    			if(isset($feedAttributeSetup[Profile::CONFIG_COMMON][Profile::CONFIG_CUSTOM_PARAMS][Profile::CONFIG_PARAM][$i][Profile::CONFIG_CODE]))
    			{
    				$code = $feedAttributeSetup[Profile::CONFIG_COMMON][Profile::CONFIG_CUSTOM_PARAMS][Profile::CONFIG_PARAM][$i][Profile::CONFIG_CODE];
    				$newCustomParams[$code] = $item[Profile::CONFIG_VALUE];
    			}
    			$i++;
    		}
    		$newConfig[Profile::CONFIG_FEED][Profile::CONFIG_COMMON][Profile::CONFIG_CUSTOM_PARAMS] = $newCustomParams;
    	}
    	
    	//custom attributes
    	$newConfig[Profile::CONFIG_FEED][Profile::CONFIG_CUSTOM_ATTRIBUTES] = [];
    	
    	//convert prefix and suffix to composed value
    	if(isset($newConfig[Profile::CONFIG_FEED][Profile::CONFIG_ATTRIBUTES]) && is_array($newConfig[Profile::CONFIG_FEED][Profile::CONFIG_ATTRIBUTES]))
    	{
    		foreach($newConfig[Profile::CONFIG_FEED][Profile::CONFIG_ATTRIBUTES] as $key => $attribute)
    		{
    			$composedValue = '';
    			if(isset($attribute[Profile::CONFIG_ATTRIBUTE_PREFIX]))
    				$composedValue .= $attribute[Profile::CONFIG_ATTRIBUTE_PREFIX];
    			if(isset($attribute[Profile::CONFIG_ATTRIBUTE_MAGENTO]))
    				$composedValue .= "{{".$attribute[Profile::CONFIG_ATTRIBUTE_MAGENTO]."}}";
    			if(isset($attribute[Profile::CONFIG_ATTRIBUTE_SUFFIX]))
    				$composedValue .= "{{".$attribute[Profile::CONFIG_ATTRIBUTE_SUFFIX]."}}";
    			 
    			if(!empty($composedValue))
    				$attribute[Profile::CONFIG_ATTRIBUTE_COMPOSED_VALUE] = $composedValue;

    			if(!empty($attribute['translate']))
    			{
    				$attribute[Profile::CONFIG_ATRIBUTE_CONVERT] = $attribute['translate'];
    			}
    			
    			if(isset($attribute[Profile::CONFIG_ATTRIBUTE_MAGENTO]))
    				$attribute[Profile::CONFIG_ATTRIBUTE_MAGENTO] = $this->attributeSource->decorateAttributeCode($attribute[Profile::CONFIG_ATTRIBUTE_MAGENTO]);
    			
    			if(isset($attribute[Profile::CONFIG_ATRIBUTE_CODE]) && $attribute[Profile::CONFIG_ATRIBUTE_CODE] == "custom_attribute")
    			{
    				$attribute[Profile::CONFIG_ATRIBUTE_TYPE] = Profile::CONFIG_ATRIBUTE_TYPE_CUSTOM;
    				$attribute[Profile::CONFIG_ATTRIBUTE_CODE] = $this->helper->createCode($attribute[Profile::CONFIG_ATTRIBUTE_LABEL]);
    				
    				$newConfig[Profile::CONFIG_FEED][Profile::CONFIG_CUSTOM_ATTRIBUTES][] = $attribute;
    				unset($newConfig[Profile::CONFIG_FEED][Profile::CONFIG_ATTRIBUTES][$key]);
    			}
    			else
	    			$newConfig[Profile::CONFIG_FEED][Profile::CONFIG_ATTRIBUTES][$key] = $attribute;
    		}
    	}
    	
    	return $newConfig;
    }
    
    /**
     * Update profile configuration, function is called once Feed configuration is changed
     * @param unknown_type $profile
     */
    protected function updateProfileFeedConfig($profile)
    {
    	$feed = $profile->getFeed();
    	if(empty($feed))
    		return;
    	$feedConfig = $feed->getAttributesSetup();
    	$profileFeedConfig = $profile->getConfigItem(Profile::CONFIG_FEED,false);
    	
    	
    	if(empty($feedConfig[Profile::CONFIG_ATTRIBUTES][Profile::CONFIG_ATTRIBUTE]) || empty($profileFeedConfig[Profile::CONFIG_ATTRIBUTES]))
    		return;
    	
    	$attributesProfile = $profileFeedConfig[Profile::CONFIG_ATTRIBUTES];
    	$attributesFeed = $feedConfig[Profile::CONFIG_ATTRIBUTES][Profile::CONFIG_ATTRIBUTE];
    	
    	//Fast check
    	if($this->attributesCompare($attributesProfile,$attributesFeed))
    		return;
    	
    	
    	$attributesResult = array();
    	$attributesProfileMap = array();
    	$attributesCustom = array();
    	
    	//separate custom attributes
    	foreach ($attributesProfile as $key => $data)
    	{
    		if(isset($data[Profile::CONFIG_ATRIBUTE_TYPE]) && $data[Profile::CONFIG_ATRIBUTE_TYPE] == Profile::CONFIG_ATRIBUTE_TYPE_CUSTOM)
    		{
    			$attributesCustom[] = $data;
    			unset($attributesProfile[$key]);
    		}
    		else
    		{
    			$code = $data[Profile::CONFIG_ATTRIBUTE_CODE];
    	
    			if(array_key_exists($code,$attributesProfileMap))
    				array_push($attributesProfileMap[$code], $data);
    			else
    				$attributesProfileMap[$code] = array($data);
    		}
    	}
  	   	    	
    	//recreate profile attributes
    	foreach ($attributesFeed as $data)
    	{
    		$index = 0;
    		if(empty($data[Profile::CONFIG_ATTRIBUTE_CODE]))
    			return;
    		else
    			$index = $data[Profile::CONFIG_ATTRIBUTE_CODE];
    	
    		if(isset($attributesProfileMap[$index][0]))
    		{
    			$profileAttribute = $attributesProfileMap[$index][0];
    			//update label, type and path
    			$updateFields = [Profile::CONFIG_ATTRIBUTE_CODE,Profile::CONFIG_ATTRIBUTE_LABEL, Profile::CONFIG_ATTRIBUTE_PATH];
    			foreach ($updateFields as $field)
    			{
    				if(isset($data[$field]))
    					$profileAttribute[$field] = $data[$field];
    			}
    			
    			$attributesResult[] = $profileAttribute;
    			array_shift($attributesProfileMap[$index]);
    		}
    		else
    		{
    			$attributesResult[] = $this->transformFeedAttributeSetupToProfile($data);
    		}
    	}
    	$attributesResult = array_merge($attributesResult,$attributesCustom);
    	
    	
    	
    	//save to config
    	$config = $profile->getConfig();
    	
    	$config[Profile::CONFIG_FEED][Profile::CONFIG_ATTRIBUTES] = $attributesResult;
    	$profile->setConfig($config);
    	$profile->save();
    }
    
    /**
     * Fast compare of both attribute arrays
     * @param unknown $attributesProfile
     * @param unknown $attributesFeed
     * @return boolean
     */
    protected function attributesCompare($attributesProfile,$attributesFeed)
    {
    	$lastIndex = 0;
    	foreach ($attributesFeed as $index => $data)
    	{
    		$lastIndex = $index;
    		$code = '';
    		if(isset($data[Profile::CONFIG_ATTRIBUTE_CODE]))
    			$code = $data[Profile::CONFIG_ATTRIBUTE_CODE];
    		else
    			return false;
    
    		if(isset($attributesProfile[$index][Profile::CONFIG_ATTRIBUTE_CODE]) && $attributesProfile[$index][Profile::CONFIG_ATTRIBUTE_CODE] == $code)
    			continue;
    		return false;
    	}
    
    	$lastIndex++;
    	if(isset($attributesProfile[$lastIndex][Profile::CONFIG_ATTRIBUTE_CODE]) &&  $attributesProfile[$lastIndex][Profile::CONFIG_ATRIBUTE_TYPE] != Profile::CONFIG_ATRIBUTE_TYPE_CUSTOM)
    		return false;
    	return true;
    }
    
    /**
     * Transforms feed attribute setup item to profile config attribute
     * @param unknown_type $attibuteSetup
     * @return string
     */
    protected function transformFeedAttributeSetupToProfile($attibuteSetup)
    {
    	foreach ($attibuteSetup as $key => $item)
    	{
    		if($key == Profile::CONFIG_ATTRIBUTE_DESCRIPTION)
    			continue;
    		else if($key == Profile::CONFIG_ATTRIBUTE_MAGENTO)
    		{
    			$attribute[$key] = $this->attributeSource->decorateAttributeCode($item);
    		}
    		else
    		{
    			$attribute[$key] = $item;
    		}
    	}
    	if(empty($attribute[Profile::CONFIG_ATTRIBUTE_CODE]) && array_key_exists(Profile::CONFIG_ATTRIBUTE_LABEL, $attribute))
    		$attribute[Profile::CONFIG_ATTRIBUTE_CODE] = $this->helper->createCode($attribute[Profile::CONFIG_ATTRIBUTE_LABEL]);
    	if(empty($attribute[Profile::CONFIG_ATTRIBUTE_LABEL]) && array_key_exists(Profile::CONFIG_ATTRIBUTE_CODE, $attribute))
    		$attribute[Profile::CONFIG_ATTRIBUTE_LABEL] = $this->helper->codeToLabel($attribute[Profile::CONFIG_ATTRIBUTE_CODE]);
    	return $attribute;
    }
    
    public function checkFtpConnection( array $config) {
        
        return $this->ftp->checkFtpConnection($config);
    }
    
    public function uploadFeed( Profile $profile) {
        
        return $this->ftp->uploadFeed( $profile);
    }
    
    /**
     * @return \Nostress\Koongo\Model\Channel\Profile\Ftp
     */
    public function getFtp() {
        return $this->ftp;
    }
}