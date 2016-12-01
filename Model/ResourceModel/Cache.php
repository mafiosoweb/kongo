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
namespace Nostress\Koongo\Model\ResourceModel;

class Cache extends \Nostress\Koongo\Model\ResourceModel\Data\Loader
{
    const STARTED = "started";
    const FINISHED = "finished";
    protected $_cacheName = '';

    protected function defineColumns()
    {
        parent::defineColumns();
    }

    public function reload($storeId)
    {
        $this->logStatus(self::STARTED, $storeId);
        $this->setStoreId($storeId);
        $this->init();
        $this->reloadTable();
        $this->logStatus(self::FINISHED, $storeId);
    }

    public function init()
    {
        $this->defineColumns();
    }

    protected function logStatus($status, $storeId)
    {
        $this->helper->log(__("%1 cache reload has %2 for store #%3", $this->_cacheName, $status, $storeId));
    }

    public function getCacheColumns($type = null)
    {
        if (!isset($this->_columns)) $this->defineColumns();
        return array();
    }
}

?>