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
use Nostress\Koongo\Helper\Data\Loader;
use Nostress\Koongo\Model\Config\Source;

class Product extends \Nostress\Koongo\Model\ResourceModel\Data\Loader
{
    const DEFAULT_VISIBILITY_VALUE = 4;
    const DEFAULT_STOCK_STATUS_VALUE = 1;
    protected $_lastCategoryProductTableAlias = null;
    protected $_defaultAttributes = array("id", "product_type", "group_id", "is_child");
    protected $rule;

    public function __construct(\Magento\Framework\Model\ResourceModel\Db\Context $context, \Nostress\Koongo\Model\Config\Source\Datetimeformat $datetimeformat, \Nostress\Koongo\Helper\Data\Loader $helper, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Tax\Model\Config $taxConfig, \Magento\Weee\Helper\Data $weeeData, \Nostress\Koongo\Model\Rule $rule, $resourcePrefix = null)
    {
        $this->rule = $rule;
        parent::__construct($context, $datetimeformat, $helper, $storeManager, $taxConfig, $weeeData, $resourcePrefix);
    }

    protected function _construct()
    {
        $this->_init("nostress_koongo_data_loader_product", "entity_id");
    }

    public function init()
    {
        parent::init();
        $select = $this->getSelect()->reset();
        $select->from([$this->getProductFlatTable(true) => $this->getProductFlatTable()], $this->_columns[$this->getProductFlatTable(true)]);
        $flatColumns = $this->prepareColumnsFromAttributes($this->getAttributes(false, true));
        if (isset($flatColumns["category_ids"])) {
            $tableAlias = self::NKCP;
            $filterCategoryIds = $this->getCondition(Loader::CONDITION_CATEGORY_IDS, []);
            if (!empty($filterCategoryIds)) $tableAlias = self::NKCPC;
            $flatColumns["category_ids"] = $tableAlias . ".category_ids";
        }
        $select->columns($flatColumns);
        return $this;
    }

    public function getMainTable($alias = false)
    {
        return $this->getProductFlatTable($alias);
    }

    protected function defineColumns()
    {
        parent::defineColumns();
        $this->_columns[$this->getProductFlatTable(true)] = array("id" => "entity_id", "product_type" => "type_id", "tax_class_id" => "tax_class_id");
        $productUrlSuffix = $this->getStoreConfig(ProductUrlPathGenerator::XML_PATH_PRODUCT_URL_SUFFIX);
        if (!empty($productUrlSuffix)) {
            $firstChar = substr($productUrlSuffix, 0, 1);
            if ($firstChar != ".") $productUrlSuffix = "." . $productUrlSuffix;
        }
        $this->_columns[$this->getProductFlatTable(true)]["url"] = "CONCAT('{$this->getStore()->getBaseUrl(/*\Magento\Framework\UrlInterface::URL_TYPE_MEDIA*/)}',CONCAT(IFNULL({$this->getProductFlatTable(true)}.url_path,{$this->getProductFlatTable(true)}.url_key),'{$productUrlSuffix}'))";
        $this->_columns[self::PCPF] = array("parent_sku" => "sku");
        $this->_columns[self::CISS] = array("stock_status" => "stock_status");
        if ($this->getData(self::STOCK_STATUS_DEPENDENCE) == Source\Stockdependence::QTY) {
            $condition = $this->getManageStockCondition(self::CISI);
            $this->_columns[self::CISS]["stock_status"] = "IF(" . self::NKCP . ".qty > 0 OR ({$condition}),1,0)";
        }
        $this->_columns[self::CISI] = array("minimum_qty_allowed_in_shopping_cart" => $this->helper->getRoundSql($this->getMinSaleQtyCondition(self::CISI), 0));
        $this->_columns[self::PCISS] = array("parent_stock_status" => self::PCISS . ".stock_status");
        $this->_columns[self::CPE] = array("update_datetime" => "DATE_FORMAT(" . self::CPE . ".updated_at,'{$this->getSqlTimestampFormat()}')", "update_date" => "DATE_FORMAT(" . self::CPE . ".updated_at,'{$this->getSqlTimestampFormat(self::DATE)}')", "update_time" => "DATE_FORMAT(" . self::CPE . ".updated_at,'{$this->getSqlTimestampFormat(self::TIME)}')", "creation_datetime" => "DATE_FORMAT(" . self::CPE . ".created_at,'{$this->getSqlTimestampFormat()}')", "creation_date" => "DATE_FORMAT(" . self::CPE . ".created_at,'{$this->getSqlTimestampFormat(self::DATE)}')", "creation_time" => "DATE_FORMAT(" . self::CPE . ".created_at,'{$this->getSqlTimestampFormat(self::TIME)}')");
        $this->_columns[self::CPR] = array("parent_id" => "parent_id", "group_id" => "IFNULL(" . self::CPR . ".parent_id,{$this->getMainTable(true)}.entity_id)", "is_child" => "(" . self::CPR . ".parent_id IS NOT NULL)", "is_parent" => "(" . self::CPR . ".parent_id IS NULL)");
        $this->_columns[self::NKCT] = array("tax_percent" => "IFNULL({$this->helper->getRoundSql("tax_percent*100")},0)");
        $this->_columns[self::NKCP] = array("qty" => "qty", "media_gallery" => "media_gallery");
        $filterCategoryIds = $this->getCondition(Loader::CONDITION_CATEGORY_IDS, []);
        if (empty($filterCategoryIds)) $this->_columns[self::NKCP]["categories"] = "(REPLACE(" . self::NKCP . ".categories,'" . self::DEF_CATEGORY_PATH_SUBST_DELIMITER . "','{$this->getCategoryPathDelimiter()}'))"; else {
            $this->_columns[self::NKCPC] = array("categories" => "(REPLACE(" . self::NKCPC . ".categories,'" . self::DEF_CATEGORY_PATH_SUBST_DELIMITER . "','{$this->getCategoryPathDelimiter()}'))");
        }
        $this->_columns[self::NKCW] = $this->getWeeeColumns();
        $this->definePriceColumns();
        $this->_columns[self::NKTC] = array("taxonomy_name" => "name", "taxonomy_id" => "id", "taxonomy_path" => "path", "taxonomy_ids_path" => "ids_path", "taxonomy_level" => "level", "taxonomy_parent_name" => "parent_name", "taxonomy_parent_id" => "parent_id");
        $this->addPrefixToOwnColumns();
    }

    protected function addPrefixToOwnColumns()
    {
        foreach ($this->_columns as $tableAlias => $tableColumns) {
            foreach ($tableColumns as $alias => $value) {
                $aliasWithPrefix = \Nostress\Koongo\Model\Config\Source\Attributes::MODULE_PATTRIBUTE_PREFIX . $alias;
                $this->_columns[$tableAlias][$aliasWithPrefix] = $value;
                unset($this->_columns[$tableAlias][$alias]);
            }
        }
    }

    protected function getDefaultAttributes()
    {
        $defaultAttributes = $this->_defaultAttributes;
        $transformedAttributes = [];
        foreach ($defaultAttributes as $attributeCode) {
            $index = \Nostress\Koongo\Model\Config\Source\Attributes::MODULE_PATTRIBUTE_PREFIX . $attributeCode;
            $transformedAttributes[$index] = [];
        }
        return $transformedAttributes;
    }

    protected function getAttributeWithProfix($attributeCode)
    {
        return \Nostress\Koongo\Model\Config\Source\Attributes::MODULE_PATTRIBUTE_PREFIX . $attributeCode;
    }

    protected function getAttributes($attributeCodesOnly = true, $flatCatalogOnly = false)
    {
        if (!$flatCatalogOnly || !is_array($this->_atttibutes)) {
            if ($attributeCodesOnly) return array_keys($this->_atttibutes); else return $this->_atttibutes;
        }
        $ownAttributes = array_keys($this->getAllColumns());
        $flatAttributes = [];
        foreach ($this->_atttibutes as $key => $attributeInfo) {
            if (!in_array($key, $ownAttributes)) $flatAttributes[$key] = $attributeInfo;
        }
        if ($attributeCodesOnly) return array_keys($flatAttributes); else return $flatAttributes;
    }

    public function joinProductEntity()
    {
        $mainTableAlias = $this->getMainTable(true);
        $joinTableAlias = self::CPE;
        $joinTable = $this->getTable("catalog_product_entity");
        $condition = "{$joinTableAlias}.entity_id = " . self::MAIN_TABLE_SUBST . ".entity_id";
        $this->joinTable($mainTableAlias, $joinTableAlias, $joinTable, false, $condition);
        return $this;
    }

    public function groupByProduct()
    {
        $select = $this->getSelect();
        $select->group($this->getMainTable(true) . ".entity_id");
    }

    public function addSortAttribute()
    {
        $sortAttribute = $this->getData(self::SORT_ATTRIBUTE);
        if (empty($sortAttribute)) return;
        $select = $this->getSelect();
        $select->columns(array(self::SORT_ATTRIBUTE_ALIAS => "IF(" . self::CPR . ".parent_id IS NOT NULL," . self::PCPF . ".{$sortAttribute},{$this->getMainTable(true)}.{$sortAttribute})"));
    }

    public function setProductsOrder()
    {
        $select = $this->getSelect();
        $sortAttribute = $this->getData(self::SORT_ATTRIBUTE);
        if (!empty($sortAttribute)) {
            $select->order("ISNULL(" . self::SORT_ATTRIBUTE_ALIAS . ") " . $this->getData(self::SORT_ORDER, "ASC"));
            $select->order(self::SORT_ATTRIBUTE_ALIAS . " " . $this->getData(self::SORT_ORDER, "ASC"));
        }
        $select->order($this->getAttributeWithProfix("group_id"));
        $select->order($this->getMainTable(true) . ".type_id");
    }

    public function joinProductRelation()
    {
        $joinTableAlias = self::CPR;
        $joinTable = $this->getTable("catalog_product_relation");
        $condition = $joinTableAlias . ".child_id=" . self::MAIN_TABLE_SUBST . ".entity_id ";
        $this->joinMainTable($joinTableAlias, $joinTable, true, $condition);
        $this->addParentsCondition();
        return $this;
    }

    public function addParentsCondition()
    {
        if ($this->parentsOnly()) {
            $select = $this->getSelect();
            $select->where(self::CPR . ".parent_id IS NULL ");
        }
    }

    protected function parentsOnly()
    {
        if ($this->getCondition(Loader::CONDITION_PARENTS_CHILDS, 0) == Source\Parentschilds::PARENTS_ONLY) return true; else return false;
    }

    protected function joinParentProductFlat()
    {
        $mainTableAlias = self::CPR;
        $joinTableAlias = self::PCPF;
        $joinTable = $this->getProductFlatTable();
        $condition = $joinTableAlias . ".entity_id=" . self::MAIN_TABLE_SUBST . ".parent_id";
        $this->joinTable($mainTableAlias, $joinTableAlias, $joinTable, true, $condition);
        return $this;
    }

    public function addVisibilityCondition()
    {
        $this->joinParentProductFlat();
        $select = $this->getSelect();
        $visibility = $this->getCondition(Loader::CONDITION_VISIBILITY);
        $parentVisibility = $this->getCondition(Loader::CONDITION_VISIBILITY_PARENT);
        $where = "";
        if (count($visibility)) {
            $where .= $select->getAdapter()->quoteInto("{$this->getMainTable(true)}.visibility IN(?)", $visibility);
        }
        if (!$this->parentsOnly() && count($parentVisibility)) {
            if (!empty($where)) {
                $where .= " OR ";
            }
            $where .= $select->getAdapter()->quoteInto(self::PCPF . ".visibility  IN(?)", $parentVisibility);
        }
        if (!empty($where)) {
            $select->where($where);
        }
    }

    public function addTypeCondition()
    {
        $allTypes = $this->helper->getProductTypes();
        $types = $this->getCondition(Loader::CONDITION_TYPES, $allTypes);
        if (empty($types)) return $this;
        if (!is_array($types)) $types = explode(",", $types);
        if (count($allTypes) != count($types)) {
            $select = $this->getSelect();
            $select->where($this->getMainTable(true) . ".type_id IN (?)", $types);
        }
        return $this;
    }

    public function addAttributeFilter()
    {
        $conditions = $this->getCondition(Loader::CONDITION_ATTRIBUTE_FILTER_CONDITIONS, []);
        if (empty($conditions)) return $this;
        $columns = $this->_columns;
        $columns[\Nostress\Koongo\Model\Rule\Condition\Product::DEFAULT_TABLE_ALIAS] = $this->getProductFlatTable(true);
        $filterCategoryIds = $this->getCondition(Loader::CONDITION_CATEGORY_IDS, []);
        if (!empty($filterCategoryIds)) $columns[self::NKCPC]["category_ids"] = "category_ids"; else$columns[self::NKCP]["category_ids"] = "category_ids";
        $this->rule->initConditions($conditions);
        $where = $this->rule->getConditions()->asSqlWhere($columns);
        if ($where !== false && $where != "()") {
            $this->getSelect()->where($where);
        }
        return $this;
    }

    protected function getManageStockCondition($tableAlias)
    {
        $manageStockValue = (int)$this->getStoreConfig(\Magento\CatalogInventory\Model\Configuration::XML_PATH_MANAGE_STOCK);
        $manageStockCondition = "";
        if ($manageStockValue) $manageStockCondition = "{$tableAlias}.use_config_manage_stock = '0' AND {$tableAlias}.manage_stock = '0' "; else$manageStockCondition = "{$tableAlias}.use_config_manage_stock = '1' OR ({$tableAlias}.use_config_manage_stock = '0' AND {$tableAlias}.manage_stock = '0') ";
        return $manageStockCondition;
    }

    protected function getMinSaleQtyCondition($tableAlias)
    {
        $globalMinSaleQty = (int)$this->getStoreConfig(\Magento\CatalogInventory\Model\Configuration::XML_PATH_MIN_SALE_QTY);
        $minSaleQtyCondition = "IF({$tableAlias}.use_config_min_sale_qty = '1', '{$globalMinSaleQty}',{$tableAlias}.min_sale_qty) ";
        return $minSaleQtyCondition;
    }

    public function addStockCondition()
    {
        if ($this->getCondition(Loader::CONDITION_EXPORT_OUT_OF_STOCK, 0) == 0) {
            $this->joinParentStock();
            $select = $this->getSelect();
            $manageParentStockCondition = $this->getManageStockCondition(self::PCISI);
            $manageStockCondition = $this->getManageStockCondition(self::CISI);
            $condition = "";
            switch ($this->getData(self::STOCK_STATUS_DEPENDENCE)) {
                case Source\Stockdependence::QTY:
                    $condition = self::NKCP . ".qty > 0 ";
                    $select->where("({$condition}) OR ({$manageStockCondition})");
                    break;
                case Source\Stockdependence::STOCK:
                    $condition = self::CISS . ".stock_status = " . self::DEFAULT_STOCK_STATUS_VALUE . " " . "AND (" . self::PCISS . ".stock_status = " . self::DEFAULT_STOCK_STATUS_VALUE . " OR " . self::PCISS . ".stock_status IS NULL )";
                    $select->where("({$condition})");
                    break;
                default:
                    $condition = self::CISS . ".stock_status = " . self::DEFAULT_STOCK_STATUS_VALUE . " AND (" . self::NKCP . ".qty > 0 OR {$this->getProductFlatTable(true)}.type_id <> 'simple') " . "AND (" . self::PCISS . ".stock_status = " . self::DEFAULT_STOCK_STATUS_VALUE . " OR " . self::PCISS . ".stock_status IS NULL OR ({$manageParentStockCondition})) ";
                    $select->where("({$condition}) OR ({$manageStockCondition})");
                    break;
            }
        }
        return $this;
    }

    public function joinStock()
    {
        $this->joinNormalStockStatus();
        $this->joinNormalStockItem();
    }

    public function joinParentStock()
    {
        $this->joinParentStockStatus();
        $this->joinParentStockItem();
    }

    protected function joinNormalStockStatus()
    {
        $mainTableAlias = $this->getMainTable(true);
        $productIdColumnName = "entity_id";
        $joinTableAlias = self::CISS;
        $this->joinStockStatus($mainTableAlias, $joinTableAlias, $productIdColumnName);
    }

    public function joinParentStockStatus()
    {
        $mainTableAlias = self::CPR;
        $productIdColumnName = "parent_id";
        $joinTableAlias = self::PCISS;
        $this->joinStockStatus($mainTableAlias, $joinTableAlias, $productIdColumnName);
    }

    protected function joinStockStatus($mainTableAlias, $joinTableAlias, $productIdColumnName)
    {
        $websiteId = $this->getData(self::STOCK_WEBSITE_ID, null);
        if (!isset($websiteId)) $websiteId = $this->getWebsiteId();
        $joinIfColumnsEmpty = $this->getCondition(Loader::CONDITION_EXPORT_OUT_OF_STOCK, 0) == 0;
        $joinTable = $this->getTable("cataloginventory_stock_status");
        $condition = $joinTableAlias . ".product_id=" . self::MAIN_TABLE_SUBST . ".{$productIdColumnName} AND {$joinTableAlias}.website_id ={$websiteId} AND {$joinTableAlias}.stock_id = " . self::DEFAULT_STOCK_ID . " ";
        $this->joinTable($mainTableAlias, $joinTableAlias, $joinTable, $joinIfColumnsEmpty, $condition);
    }

    protected function joinNormalStockItem()
    {
        $mainTableAlias = $this->getMainTable(true);
        $productIdColumnName = "entity_id";
        $joinTableAlias = self::CISI;
        $this->joinStockItem($mainTableAlias, $joinTableAlias, $productIdColumnName);
    }

    public function joinParentStockItem()
    {
        $mainTableAlias = self::CPR;
        $productIdColumnName = "parent_id";
        $joinTableAlias = self::PCISI;
        $this->joinStockItem($mainTableAlias, $joinTableAlias, $productIdColumnName);
    }

    protected function joinStockItem($mainTableAlias, $joinTableAlias, $productIdColumnName)
    {
        $joinIfColumnsEmpty = $this->getCondition(Loader::CONDITION_EXPORT_OUT_OF_STOCK, 0) == 0;
        if ($this->getData(self::STOCK_STATUS_DEPENDENCE) == Source\Stockdependence::QTY) $joinIfColumnsEmpty = true;
        $joinTable = $this->getTable("cataloginventory_stock_item");
        $condition = $joinTableAlias . ".product_id=" . self::MAIN_TABLE_SUBST . ".{$productIdColumnName} AND {$joinTableAlias}.stock_id = " . self::DEFAULT_STOCK_ID . " ";
        $this->joinTable($mainTableAlias, $joinTableAlias, $joinTable, $joinIfColumnsEmpty, $condition);
    }

    public function joinCategoryFlat()
    {
        return $this->_joinCategoryFlat(false);
    }

    public function joinLeftCategoryFlat()
    {
        return $this->_joinCategoryFlat(true);
    }

    protected function _joinCategoryFlat($joinLeft = false)
    {
        $select = $this->getSelect();
        $mainTableAlias = $this->_lastCategoryProductTableAlias;
        $joinTableAlias = $this->getCategoryFlatTable(true);
        $joinTable = $this->getCategoryFlatTable();
        $columns = $this->getColumns($joinTableAlias);
        $onColumn = "category_id";
        if ($mainTableAlias == self::NKCP || $mainTableAlias == self::NKCPC) $onColumn = "main_category_id";
        if (!$joinLeft) {
            $select->join(array($joinTableAlias => $joinTable), $joinTableAlias . ".entity_id=" . $mainTableAlias . "." . $onColumn, $columns);
        } else {
            $select->joinLeft(array($joinTableAlias => $joinTable), $joinTableAlias . ".entity_id=" . $mainTableAlias . "." . $onColumn, $columns);
        }
        $this->joinCategoryPath();
        return $this;
    }

    public function joinTaxonomy()
    {
        $selectedCols = $this->getColumns(self::NKTC);
        $taxonomyCode = $this->getData(self::TAXONOMY_CODE);
        if (empty($selectedCols) || empty($taxonomyCode)) return false;
        $channelCacheTableAlias = self::NKCCHC;
        $channelCacheTable = $this->getTable("nostress_koongo_cache_channelcategory");
        $condition = "{$channelCacheTableAlias}.product_id = " . self::MAIN_TABLE_SUBST . ".entity_id AND {$channelCacheTableAlias}.profile_id = '{$this->getProfileId()}' ";
        $this->joinMainTable($channelCacheTableAlias, $channelCacheTable, true, $condition);
        $channelCategoriesTableAlias = self::NKTC;
        $channelCategoriesTable = $this->getTable("nostress_koongo_taxonomy_category");
        $condition = "{$channelCategoriesTableAlias}.hash = {$channelCacheTableAlias}.hash " . "AND {$channelCategoriesTableAlias}.taxonomy_code = '{$taxonomyCode}' ";
        $taxonomyLocale = $this->getData(self::TAXONOMY_LOCALE);
        if (!empty($taxonomyLocale)) $condition .= "AND {$channelCategoriesTableAlias}.locale = '{$taxonomyLocale}' ";
        $this->joinMainTable($channelCategoriesTableAlias, $channelCategoriesTable, false, $condition);
        return true;
    }

    public function joinWeee()
    {
        if (!$this->isWeeeEnabled()) return $this;
        $joinTableAlias = self::NKCW;
        $joinTable = $this->getTable("nostress_koongo_cache_weee");
        $condition = "{$joinTableAlias}.product_id = " . self::MAIN_TABLE_SUBST . ".entity_id AND {$joinTableAlias}.website_id = '{$this->getWebsiteId()}' ";
        $this->joinMainTable($joinTableAlias, $joinTable, true, $condition);
        return $this;
    }

    public function joinTax()
    {
        $joinTableAlias = self::NKCT;
        $joinTable = $this->getTable("nostress_koongo_cache_tax");
        $condition = "{$joinTableAlias}.tax_class_id = " . self::MAIN_TABLE_SUBST . ".tax_class_id AND {$joinTableAlias}.store_id = {$this->getStoreId()}";
        $joinIfColumnsEmpty = true;
        $this->joinMainTable($joinTableAlias, $joinTable, $joinIfColumnsEmpty, $condition);
        return $this;
    }

    public function joinProductCache()
    {
        $joinTableAlias = self::NKCP;
        $joinTable = $this->getTable("nostress_koongo_cache_product");
        $condition = "{$joinTableAlias}.product_id = " . self::MAIN_TABLE_SUBST . ".entity_id AND {$joinTableAlias}.store_id = {$this->getStoreId()} ";
        $this->_lastCategoryProductTableAlias = $joinTableAlias;
        $this->joinMainTable($joinTableAlias, $joinTable, true, $condition);
        return $this;
    }

    public function joinProfileCategoryCache()
    {
        $filterCategoryIds = $this->getCondition(Loader::CONDITION_CATEGORY_IDS, []);
        if (empty($filterCategoryIds)) return $this;
        $joinTableAlias = self::NKCPC;
        $joinTable = $this->getTable("nostress_koongo_cache_profilecategory");
        $condition = "{$joinTableAlias}.product_id = " . self::MAIN_TABLE_SUBST . ".entity_id AND {$joinTableAlias}.profile_id = {$this->getProfileId()} ";
        $this->_lastCategoryProductTableAlias = $joinTableAlias;
        $this->joinMainTable($joinTableAlias, $joinTable, true, $condition, false);
        return $this;
    }

    protected function definePriceColumns()
    {
        $taxRateColumnName = self::NKCT . ".tax_percent";
        $weeeColumn = "";
        if ($this->isWeeeEnabled()) {
            $weeeColumn = self::NKCW . "." . self::WEEE_COLUMN_TOTAL;
        }
        $columns = array("price_final_exclude_tax" => $this->getPriceColumnFormat(self::NKCP . "." . "min_price", false, $weeeColumn), "price_final_include_tax" => $this->getPriceColumnFormat(self::NKCP . "." . "min_price", true, $weeeColumn), "price_original_exclude_tax" => $this->getPriceColumnFormat(self::NKCP . "." . "price", false, $weeeColumn), "price_original_include_tax" => $this->getPriceColumnFormat(self::NKCP . "." . "price", true, $weeeColumn), "price_discount_percent" => "ROUND(((" . self::NKCP . ".price - " . self::NKCP . ".min_price)*100)/" . self::NKCP . ".price,0)", "price_discount_exclude_tax" => $this->getPriceColumnFormat("(" . self::NKCP . "." . "price - " . self::NKCP . "." . "min_price)", false), "price_discount_include_tax" => $this->getPriceColumnFormat("(" . self::NKCP . "." . "price - " . self::NKCP . "." . "min_price)", true),);
        $this->_columns[self::NKCP] = array_merge($this->_columns[self::NKCP], $columns);
    }

    protected function getPriceColumnFormat($comunName, $includeTax, $weeeColumn = "")
    {
        $originalPricesIncludesTax = $this->_taxConfig->priceIncludesTax($this->getStore());
        $currencyRate = $this->helper->getStoreCurrencyRate($this->getStore(), $this->getCurrency());
        $taxRateColumnName = self::NKCT . ".tax_percent";
        $weeeTaxable = $this->isWeeeTaxable();
        $weeeColumnTaxable = $weeeColumnNonTaxable = "";
        if ($this->isWeeeEnabled() && !empty($weeeColumn)) {
            $weeeColumn = "IFNULL({$weeeColumn},0)";
            switch ($this->getPriceDisplayType()) {
                case\Magento\Weee\Model\Tax::DISPLAY_INCL:
                case\Magento\Weee\Model\Tax::DISPLAY_INCL_DESCR:
                    if ($includeTax && $weeeTaxable) {
                        $weeeColumnTaxable = $weeeColumn;
                    } else if ($originalPricesIncludesTax && !$includeTax && $weeeTaxable) {
                        $weeeColumnTaxable = $weeeColumn;
                    } else$weeeColumnNonTaxable = $weeeColumn;
                case\Magento\Weee\Model\Tax::DISPLAY_EXCL_DESCR_INCL:
                case\Magento\Weee\Model\Tax::DISPLAY_EXCL:
                default:
                    $weeeTaxable = false;
                    break;
            }
        }
        $columnFormat = $this->helper->getPriceColumnFormat($comunName, $taxRateColumnName, $currencyRate, $originalPricesIncludesTax, $includeTax, true, $weeeColumnTaxable, $weeeColumnNonTaxable);
        return $columnFormat;
    }

    protected function getWeeeColumns()
    {
        $originalPricesIncludesTax = $this->_taxConfig->priceIncludesTax($this->getStore());
        $currencyRate = $this->helper->getStoreCurrencyRate($this->getStore(), $this->getCurrency());
        $taxRateColumnName = self::NKCT . ".tax_percent";
        $weeeTaxable = $this->isWeeeTaxable();
        $fptTotalInclTax = $this->helper->getWeeeColumnFormat(self::WEEE_COLUMN_TOTAL, $taxRateColumnName, $currencyRate, $originalPricesIncludesTax, $weeeTaxable, true);
        $fptTotalExclTax = $this->helper->getWeeeColumnFormat(self::WEEE_COLUMN_TOTAL, $taxRateColumnName, $currencyRate, $originalPricesIncludesTax, false, true);
        $columns = ["fixed_product_tax" . self::INCLUDE_TAX_SUFFIX => $fptTotalInclTax, "fixed_product_tax" . self::EXCLUDE_TAX_SUFFIX => $fptTotalExclTax];
        $attributes = $this->convertWeeeAttributesToColumnNames();
        foreach ($attributes as $code) {
            $columns[$code . self::INCLUDE_TAX_SUFFIX] = $this->helper->getWeeeColumnFormat($code, $taxRateColumnName, $currencyRate, $originalPricesIncludesTax, $weeeTaxable, true);
            $columns[$code . self::EXCLUDE_TAX_SUFFIX] = $this->helper->getWeeeColumnFormat($code, $taxRateColumnName, $currencyRate, $originalPricesIncludesTax, false, true);
        }
        return $columns;
    }
}
?>