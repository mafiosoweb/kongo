<?php /*
Magento Module developed by NoStress Commerce

 NOTICE OF LICENSE

 This source file is subject to the Open Software License (OSL 3.0)
 that is bundled with this package in the file LICENSE.txt.
 It is also available through the world-wide-web at this URL:
 http://opensource.org/licenses/osl-3.0.php
 If you did of the license and are unable to
 obtain it through the world-wide-web, please send an email
 to info@nostresscommerce.cz so we can send you a copy immediately.

 @copyright Copyright (c) 2015 NoStress Commerce (http://www.nostresscommerce.cz)

*/
namespace Nostress\Koongo\Model\ResourceModel\Cache;

class Channelcategory extends \Nostress\Koongo\Model\ResourceModel\Cache
{
    const RULE_MAGENTO_CATEGORIES = "magento_categories";
    const RULE_CHANNEL_CATEGORY = "channel_category";
    protected $_cacheName = 'Channel category';
    protected $_mainTableAlias = self::NKCCHC;
    protected $_profileId = '';
    protected $_locale = '';
    protected $_storeId = '';
    protected $_taxonomyCode = '';
    protected $_rules = '';

    public function _construct()
    {
        $this->_init("nostress_koongo_cache_channelcategory", "product_id");
    }

    protected function reloadTable()
    {
        $this->cleanMainTable();
        $this->applyRules();
    }

    protected function cleanMainTable()
    {
        $this->helper->log(__("Clean nostress_koongo_cache_channelcategory records for profile #%1", $this->_profileId));
        $this->getConnection()->delete($this->getMainTable(), ["profile_id = ?" => $this->_profileId]);
    }

    protected function applyRules()
    {
        if (!isset($this->_rules) || !is_array($this->_rules)) return;
        foreach ($this->_rules as $rule) {
            if (isset($rule[self::RULE_MAGENTO_CATEGORIES]) && isset($rule[self::RULE_CHANNEL_CATEGORY])) {
                $magentoCategories = explode(",", $rule[self::RULE_MAGENTO_CATEGORIES]);
                $channelCategoryId = $rule[self::RULE_CHANNEL_CATEGORY];
                $select = $this->getSelectChannelCategoryProductRecordsSql($magentoCategories, $channelCategoryId, $this->_profileId, $this->_storeId);
                $this->insertRecords($select);
            }
        }
    }

    protected function insertRecords($select)
    {
        $sql = $select->insertIgnoreFromSelect($this->getMainTable(), array("profile_id", "product_id", "hash"));
        $this->runQuery($sql, $this->getMainTable(), "Insert records. Filled columns: profile_id, product_id, hash.");
    }

    public function setProfileId($profileId)
    {
        $this->_profileId = $profileId;
    }

    public function setStoreId($storeId)
    {
        $this->_storeId = $storeId;
    }

    public function setTaxonomyCode($taxonomyCode)
    {
        $this->_taxonomyCode = $taxonomyCode;
    }

    public function setLocale($locale)
    {
        $this->_locale = $locale;
    }

    public function setRules($rules)
    {
        $this->_rules = $rules;
    }

    protected function getSelectChannelCategoryProductRecordsSql($magentoCategories, $channelCategoryHash, $profileId, $storeId)
    {
        $catalogCategoryProductAlias = self::CCP;
        $catalogCategoryProduct = $this->getTable("catalog_category_product");
        $select = $this->getEmptySelect();
        $select->from(array($catalogCategoryProductAlias => $catalogCategoryProduct), array("profile_id" => "('{$profileId}')", "product_id" => "{$catalogCategoryProductAlias}.product_id", "hash" => "('{$channelCategoryHash}')"));
        $select->where($catalogCategoryProductAlias . ".category_id IN (?)", $magentoCategories);
        return $select;
    }
}

?>