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

class Profilecategory extends \Nostress\Koongo\Model\ResourceModel\Cache\Product
{
    protected $_cacheName = 'Profile category';
    protected $_mainTableAlias = self::NKCPC;
    protected $_lowestLevel = 0;
    protected $_allowInactiveCategoriesExport = "1";
    protected $_profileId = 0;
    protected $_categoryIds = [];

    public function _construct()
    {
        $this->_init("nostress_koongo_cache_profilecategory", "product_id");
    }

    protected function defineColumns()
    {
        parent::defineColumns();
        $this->_columns[self::NKCCP]["category_path"] = "(REPLACE(" . self::NKCCP . ".category_path,'" . self::DEF_CATEGORY_PATH_DELIMITER . "','" . self::DEF_CATEGORY_PATH_SUBST_DELIMITER . "'))";
    }

    protected function reloadTable()
    {
        $this->cleanMainTable();
        $this->insertRecords();
        $this->updateProductCategoryId();
        $this->updateProductCategories();
        $this->updateParentToChildsCategoryInfo();
    }

    protected function cleanMainTable()
    {
        $this->helper->log(__("Clean nostress_koongo_cache_profilecategory records for profile #%1", $this->_profileId));
        $this->getConnection()->delete($this->getMainTable(), ["profile_id = ?" => $this->_profileId]);
    }

    protected function insertRecords()
    {
        $sql = $this->getInsertRecordsSql();
        $this->runQuery($sql, $this->getMainTable(), "Insert records. Filled columns: profile_id, product_id, main_category_max_level .");
    }

    protected function updateProductCategoryId()
    {
        $sql = $this->getMaxLevelCategoryIdSql();
        $this->runQuery($sql, $this->getMainTable(), "Update category id. Category with max level is selected.");
    }

    protected function updateProductCategories()
    {
        $sql = $this->getProductCategoriesSql();
        $this->runQuery($sql, $this->getMainTable(), "Update product categories");
    }

    protected function updateParentToChildsCategoryInfo()
    {
        $sql = $this->getInsertChildProductsRecordsSql();
        $this->runQuery($sql, $this->getMainTable(), "Update category info from parent to child.");
    }

    public function setLowestLevel($level)
    {
        if (isset($level) && is_numeric($level) && $level >= 0) $this->_lowestLevel = $level;
    }

    public function setProfileId($profileId)
    {
        $this->_profileId = $profileId;
    }

    public function setCategoryIds($categoryIds)
    {
        $this->_categoryIds = $categoryIds;
    }

    public function setAllowInactiveCategoriesExport($status)
    {
        if (isset($status)) $this->_allowInactiveCategoriesExport = $status;
    }

    protected function allowInactiveCategoriesExport()
    {
        return $this->_allowInactiveCategoriesExport;
    }

    protected function getInsertRecordsSql()
    {
        $select = $this->getSelectCategoryMaxLevelSql($this->_categoryIds, $this->_profileId);
        $sql = $this->getConnection()->insertFromSelect($select, $this->getMainTable(), ["profile_id", "product_id", "main_category_max_level"]);
        return $sql;
    }

    protected function getSelectCategoryMaxLevelSql($categoryIds, $profileId)
    {
        $mainTableAlias = $this->getProductFlatTable(true);
        $mainTable = $this->getProductFlatTable();
        $catProdTableAlias = self::CCPI;
        $catProdTable = $this->getTable("catalog_category_product_index");
        $catTableAlias = $this->getCategoryFlatTable(true);
        $catTable = $this->getCategoryFlatTable();
        $select = $this->getEmptySelect();
        $select->from([$mainTableAlias => $mainTable], ["profile_id" => "('{$profileId}')", "product_id" => "entity_id"]);
        $select->join([$catProdTableAlias => $catProdTable], $catProdTableAlias . ".product_id=" . $mainTableAlias . ".entity_id AND " . $catProdTableAlias . ".store_id = " . $this->getStoreId() . " AND " . $catProdTableAlias . ".category_id IN (" . $categoryIds . ")", null);
        $select->joinLeft([$catTableAlias => $catTable], $catProdTableAlias . ".category_id=" . $catTableAlias . ".entity_id", ["main_category_max_level" => "MAX(level)"]);
        $select->group($mainTableAlias . ".entity_id");
        return $select;
    }

    protected function getMaxLevelCategoryIdSql()
    {
        $mainTableAlias = "main_table";
        $mainTable = $this->getMainTable();
        $catProdTableAlias = self::CCPI;
        $catProdTable = $this->getTable("catalog_category_product_index");
        $catTableAlias = $this->getCategoryFlatTable(true);
        $catTable = $this->getCategoryFlatTable();
        $sql = "UPDATE {$mainTable} AS {$mainTableAlias} ";
        $sql .= "LEFT JOIN {$catProdTable} AS {$catProdTableAlias} ON {$catProdTableAlias}.product_id = {$mainTableAlias}.product_id AND {$catProdTableAlias}.store_id = {$this->getStoreId()} ";
        $sql .= "INNER JOIN {$catTable} AS {$catTableAlias} ON {$catTableAlias}.entity_id = {$catProdTableAlias}.category_id AND {$catTableAlias}.level = {$mainTableAlias}.main_category_max_level ";
        $sql .= "SET {$mainTableAlias}.main_category_id = {$catTableAlias}.entity_id ";
        $sql .= "WHERE {$mainTableAlias}.profile_id = {$this->_profileId} ";
        return $sql;
    }

    protected function getProductCategoriesSql()
    {
        $mainTable = $this->getTable("catalog_category_product_index");
        $mainTableAlias = self::CCPI;
        $joinTableAlias = $this->getCategoryFlatTable(true);
        $joinTable = $this->getCategoryFlatTable();
        $select = $this->getEmptySelect();
        $select->from(array($mainTableAlias => $mainTable), array("product_id", "({$this->getStoreId()}) as store_id"));
        $select->join(array($joinTableAlias => $joinTable), $joinTableAlias . ".entity_id=" . $mainTableAlias . ".category_id AND " . $mainTableAlias . ".category_id IN (" . $this->_categoryIds . ")", null);
        $select->where($mainTableAlias . ".store_id=?", $this->getStoreId());
        $select->where($joinTableAlias . ".level>=?", $this->_lowestLevel);
        if (!$this->allowInactiveCategoriesExport()) $select->where($joinTableAlias . ".is_active=?", self::CATEGORY_ACTIVE);
        $select->group("product_id");
        $select = $this->_joinCategoryPath($select, false, $mainTableAlias, "category_id");
        $columns = $this->getCacheColumns("category");
        $select->columns($this->helper->groupConcatColumns($columns));
        $select->columns($this->helper->groupConcatColumns(["{$joinTableAlias}.entity_id"], ",", "category_ids"));
        $updateSql = "UPDATE {$this->getMainTable()} AS {$this->_mainTableAlias} ";
        $updateSql .= "INNER JOIN ( {$select->__toString()} ) ";
        $updateSql .= "AS categories ON {$this->_mainTableAlias}.product_id = categories.product_id AND  {$this->_mainTableAlias}.profile_id = {$this->_profileId} ";
        $updateSql .= "SET  {$this->_mainTableAlias}.categories =  categories.concat_colum, {$this->_mainTableAlias}.category_ids =  categories.category_ids ";
        return $updateSql;
    }

    protected function getInsertChildProductsRecordsSql()
    {
        $select = $this->getSelectChildProductRecordSql();
        $sql = $select->insertIgnoreFromSelect($this->getMainTable(), array("profile_id", "product_id", "main_category_id", "main_category_max_level", "categories", "category_ids"));
        return $sql;
    }

    protected function getSelectChildProductRecordSql()
    {
        $mainTableAlias = "main_table";
        $mainTable = $this->getMainTable();
        $catProdRelAlias = self::CPR;
        $catProdRel = $this->getTable("catalog_product_relation");
        $parentCacheAlias = "pnkcpc";
        $select = $this->getEmptySelect();
        $select->from(array($mainTableAlias => $mainTable), array("profile_id" => "('{$this->_profileId}')", "product_id" => "{$catProdRelAlias}.child_id", "main_category_id", "main_category_max_level", "categories", "category_ids"));
        $select->join(array($catProdRelAlias => $catProdRel), "{$catProdRelAlias}.parent_id = {$mainTableAlias}.product_id", null);
        $select->where($mainTableAlias . ".profile_id=?", $this->_profileId);
        return $select;
    }
}

?>