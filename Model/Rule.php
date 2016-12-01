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
 * Rule extensiosn for own product filtering
 *
 * @category Nostress
 * @package Nostress_Koongo
 *
 */
namespace Nostress\Koongo\Model;

class Rule extends \Magento\CatalogRule\Model\Rule
{
	/**
	 * @param \Magento\Framework\Model\Context $context
	 * @param \Magento\Framework\Registry $registry
	 * @param \Magento\Framework\Data\FormFactory $formFactory
	 * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
	 * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
	 * @param \Magento\Store\Model\StoreManagerInterface $storeManager
	 * @param \Magento\CatalogRule\Model\Rule\Condition\CombineFactory $combineFactory
	 * @param \Magento\CatalogRule\Model\Rule\Action\CollectionFactory $actionCollectionFactory
	 * @param \Magento\Catalog\Model\ProductFactory $productFactory
	 * @param \Magento\Framework\Model\ResourceModel\Iterator $resourceIterator
	 * @param \Magento\Customer\Model\Session $customerSession
	 * @param \Magento\CatalogRule\Helper\Data $catalogRuleData
	 * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypesList
	 * @param \Magento\Framework\Stdlib\DateTime $dateTime
	 * @param \Magento\CatalogRule\Model\Indexer\Rule\RuleProductProcessor $ruleProductProcessor
	 * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
	 * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
	 * @param array $relatedCacheTypes
	 * @param array $data
	 * @SuppressWarnings(PHPMD.ExcessiveParameterList)
	 */
	public function __construct(
		\Magento\Framework\Model\Context $context,
		\Magento\Framework\Registry $registry,
		\Magento\Framework\Data\FormFactory $formFactory,
		\Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
		\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
		\Magento\Store\Model\StoreManagerInterface $storeManager,		
		\Magento\CatalogRule\Model\Rule\Condition\CombineFactory $combineFactory,
		\Nostress\Koongo\Model\Rule\Condition\CombineFactory $koongoCombineFactory,
		\Magento\CatalogRule\Model\Rule\Action\CollectionFactory $actionCollectionFactory,
		\Magento\Catalog\Model\ProductFactory $productFactory,
		\Magento\Framework\Model\ResourceModel\Iterator $resourceIterator,
		\Magento\Customer\Model\Session $customerSession,
		\Magento\CatalogRule\Helper\Data $catalogRuleData,
		\Magento\Framework\App\Cache\TypeListInterface $cacheTypesList,
		\Magento\Framework\Stdlib\DateTime $dateTime,
		\Magento\CatalogRule\Model\Indexer\Rule\RuleProductProcessor $ruleProductProcessor,
		\Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
		\Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
		array $relatedCacheTypes = [],
		array $data = []
	) {
		
		parent::__construct($context, $registry, $formFactory, $localeDate, $productCollectionFactory, 
				$storeManager, $combineFactory, $actionCollectionFactory, $productFactory, $resourceIterator, 
				$customerSession, $catalogRuleData, $cacheTypesList, $dateTime, $ruleProductProcessor,$resource,
				$resourceCollection, $relatedCacheTypes, $data);
		
 		$this->_combineFactory = $koongoCombineFactory;
	}
	

    public function parseConditionsPost(array $rulePost)
    {
        $arr = $this->_convertFlatToRecursive($rulePost);
        if (isset($arr['conditions'])) {
            return $arr['conditions'][1];
        } else {
            return false;
        }
    }
    
    public function initConditions( array $conditionsConfig)
    {
        $this->getConditions()->setConditions(array())->loadArray( $conditionsConfig);
        return $this;
    }
}