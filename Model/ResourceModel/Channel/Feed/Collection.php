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
namespace Nostress\Koongo\Model\ResourceModel\Channel\Feed;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'entity_id';
    const COL_LINK = "link";
    const COL_CODE = "code";
    const COL_FILE_TYPE = "file_type";
    const COL_ENABLED = "enabled";
    const COUNTRY = "country";
    const LABEL = "label";
    const DEF_ENABLED = "1";
    const YES = "1";
    const NO = "0";

    protected function _construct()
    {
        $this->_init("Nostress\\Koongo\Model\\Channel\Feed", "Nostress\Koongo\Model\ResourceModel\Channel\\Feed");
    }

    public function toOptionArray($enabled = null, $addFileType = null)
    {
        $additional = array(self::COUNTRY => self::COUNTRY);
        if (isset($enabled)) $additional[self::COL_ENABLED] = self::COL_ENABLED;
        if (isset($addFileType)) $additional[self::COL_FILE_TYPE] = self::COL_FILE_TYPE;
        $options = $this->_toOptionArray(self::COL_CODE, self::COL_LINK, $additional);
        foreach ($options as $key => $option) {
            if (isset($option[self::COL_ENABLED]) && $option[self::COL_ENABLED] != self::DEF_ENABLED) {
                unset($options[$key]);
                continue;
            }
            if (isset($option[self::COL_FILE_TYPE]) && $addFileType == self::YES) {
                $options[$key][self::LABEL] .= " - " . $option[self::COL_FILE_TYPE];
            }
        }
        return $options;
    }

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
}

?>