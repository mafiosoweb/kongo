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
namespace Nostress\Koongo\Model\ResourceModel\Taxonomy\Category;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init("Nostress\\Koongo\\Model\\Taxonomy\\Category", "Nostress\Koongo\Model\ResourceModel\\Taxonomy\\Category");
    }

    const ENGINE_CODE = "engine_code";
    const LOCALE = "locale";
    protected $_emptyItem = null;
    protected $_engine = null;
    protected $_locale = null;

    public function addFieldsToFilter($fields)
    {
        if (!is_array($fields)) return;
        foreach ($fields as $field => $condition) {
            $this->addFieldToFilter($field, $condition);
        }
    }

    public function addFieldsToSelect($fields)
    {
        if (!is_array($fields)) return;
        foreach ($fields as $alias => $field) {
            $this->addFieldToSelect($field, $alias);
        }
    }

    public function getItems($indexField = \Nostress\Koongo\Model\Taxonomy\Category::C_ID)
    {
        $items = array();
        foreach (parent::getItems() as $item) {
            $record = array();
            foreach ($item->getData() as $label => $value) {
                $record[$label] = $value;
            }
            if (isset($indexField) && isset($record[$indexField])) {
                $items[$record[$indexField]] = $record;
            } else {
                $items[] = $record;
            }
        }
        return $items;
    }
}

?>