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
namespace Nostress\Koongo\Model\ResourceModel\Data\Loader;

use Magento\CatalogUrlRewrite\Model\ProductUrlPathGenerator;
use Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator;
use Nostress\Koongo\Helper\Data\Loader;
use Nostress\Koongo\Model\Config\Source;

class Category extends \Nostress\Koongo\Model\ResourceModel\Data\Loader
{
    const ZERO_LEVEL = 0;

    public function init()
    {
        parent::init();
        $select = $this->getSelect();
        $mainTableAlias = $this->getMainTable(true);
        $select->from(array($mainTableAlias => $this->getMainTable()), $this->getColumns($mainTableAlias));
        $select->distinct();
        if (!$this->getData(self::ALLOW_INACTIVE_CATEGORIES_EXPORT, "1")) $select->where($mainTableAlias . ".is_active=?", self::CATEGORY_ACTIVE);
        $select->where($mainTableAlias . ".level>=?", $this->getCategoryLowestLevel());
        return $this;
    }

    public function getMainTable($alias = false)
    {
        return $this->getCategoryFlatTable($alias);
    }

    protected function defineColumns()
    {
        $defaultCatPathDelim = self::DEF_CATEGORY_PATH_DELIMITER;
        parent::defineColumns();
        $this->_columns[$this->getCategoryFlatTable(true)] = array("id" => "entity_id", "name" => "name", "path_ids" => "(SUBSTRING_INDEX({$this->getCategoryFlatTable(true)}.path,'{$defaultCatPathDelim}',-{$this->getCategoryFlatTable(true)}.level+{$this->getCategoryLowestLevel(true)}))", "level" => "({$this->getCategoryFlatTable(true)}.level - {$this->getCategoryLowestLevel()})", "parent_id" => "parent_id", "url_key" => "url_key", "path_url_key" => "(REPLACE(REPLACE(IFNULL({$this->getCategoryFlatTable(true)}.url_path,''), '.html',''),'" . self::DEF_CATEGORY_PATH_DELIMITER . "','-'))", "meta_description" => "meta_description", "meta_title" => "meta_title", "meta_keywords" => "meta_keywords", "description" => "description");
        $this->_columns[self::NKCCP] = array("path" => "category_path", "root_name" => "category_root_name", "root_id" => "category_root_id");
        $categoryUrlSuffix = $this->getStoreConfig(CategoryUrlPathGenerator::XML_PATH_CATEGORY_URL_SUFFIX);
        if (!empty($categoryUrlSuffix)) {
            $firstChar = substr($categoryUrlSuffix, 0, 1);
            if ($firstChar != ".") $categoryUrlSuffix = "." . $categoryUrlSuffix;
        }
        $this->_columns[$this->getCategoryFlatTable(true)]["url"] = "(CONCAT('{$this->getStore()->getBaseUrl()}',IFNULL({$this->getCategoryFlatTable(true)}.url_path,CONCAT({$this->getCategoryFlatTable(true)}.url_key,'{$categoryUrlSuffix}'))))";
        $userCatPathDelim = $this->getCategoryPathDelimiter();
        if ($userCatPathDelim !== $defaultCatPathDelim) $this->_columns[self::NKCCP]["path"] = "REPLACE(" . self::NKCCP . ".category_path,'{$defaultCatPathDelim}','{$userCatPathDelim}')";
        $this->_columns[$this->getCategoryFlatTable(true, null, true)] = array("parent_name" => "name");
    }

    public function joinProductFilter()
    {
        $this->joinExportCategoryProduct();
    }

    public function orderByLevel()
    {
        $select = $this->getSelect();
        $select->order($this->getMainTable(true) . ".level");
    }

    public function addCategoryFilter()
    {
        $categoryIds = $this->getCondition(Loader::CONDITION_CATEGORY_IDS, []);
        if (empty($categoryIds)) return $this; else if (!is_array($categoryIds)) {
            $categoryIds = explode(",", $categoryIds);
        }
        $select = $this->getSelect();
        $where = $select->getAdapter()->quoteInto($this->getCategoryFlatTable(true) . ".entity_id IN(?)", $categoryIds);
        $select->where($where);
        return $this;
    }

    protected function getColumns($tableAlias, $defualt = null, $groupConcat = false, $addTablePrefix = false)
    {
        if (array_key_exists($tableAlias, $this->_columns)) {
            $result = $this->_columns[$tableAlias];
        } else {
            $result = $defualt;
        }
        if ($groupConcat) $result = $this->groupConcatColumns($result);
        if ($addTablePrefix) $result = $this->addTablePrefix($tableAlias, $result);
        return $result;
    }

    public function getCategoryLowestLevel($modify = false)
    {
        $level = $this->getData(self::CATEGORY_LOWEST_LEVEL);
        if (empty($level)) $level = self::ZERO_LEVEL;
        if (!$modify) {
            return $level;
        } else {
            $level--;
            if ($level < 0) $level = 0;
            return $level;
        }
    }
}

?>