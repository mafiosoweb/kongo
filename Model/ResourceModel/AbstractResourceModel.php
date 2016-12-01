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

abstract class AbstractResourceModel extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    const TIME = "time";
    const DATE = "date";
    const DATE_TIME = "date_time";

    protected function runQuery($queryString, $tableName = "", $message = "", $useTransaction = true)
    {
        if (!empty($message)) {
            $this->helper->log("Table: {$tableName}");
            $this->helper->log($message);
            $this->helper->log(base64_encode($queryString));
        }
        if (!isset($queryString) || $queryString == "") return $this;
        if ($useTransaction) $this->transactionManager->start($this->getConnection());
        try {
            $this->getConnection()->query($queryString);
            if ($useTransaction) $this->commit();
        } catch (\Exception$e) {
            if ($useTransaction) $this->transactionManager->rollBack();
            throw$e;
        }
        return $this;
    }

    protected function runSelectQuery($select, $tableName = "", $message = "")
    {
        if (!empty($message)) {
            $queryString = $select->__toString();
            $this->helper->log("Table: {$tableName}");
            $this->helper->log($message);
            $this->helper->log(base64_encode($queryString));
        }
        return $this->getConnection()->fetchAll($select);
    }

    protected function runOneRowSelectQuery($queryString)
    {
        return $this->getConnection()->fetchRow($queryString);
    }
}

?>