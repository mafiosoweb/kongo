<?php 
/*
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

class Tax extends \Nostress\Koongo\Model\ResourceModel\Cache
{
    protected $_cacheName = 'Tax';
    protected $taxClassModel;
    protected $taxCalculation;
    protected $_rateRequest;

    public function __construct(\Magento\Framework\Model\ResourceModel\Db\Context $context, \Nostress\Koongo\Model\Config\Source\Datetimeformat $datetimeformat, \Nostress\Koongo\Helper\Data\Loader $helper, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Tax\Model\Config $taxConfig, \Magento\Weee\Helper\Data $weeeData, \Magento\Tax\Model\ClassModel $taxClassModel, \Magento\Tax\Model\Calculation $taxCalculation, $resourcePrefix = null)
    {
        $this->taxClassModel = $taxClassModel;
        $this->taxCalculation = $taxCalculation;
        parent::__construct($context, $datetimeformat, $helper, $storeManager, $taxConfig, $weeeData, $resourcePrefix);
    }

    public function _construct()
    {
        $this->_init("nostress_koongo_cache_tax", "tax_class_id");
    }

    protected function reloadTable()
    {
        $this->cleanMainTable();
        $this->updateRates();
    }

    protected function updateRates()
    {
        $storeId = $this->getStoreId();
        $productTaxClassCollection = $this->taxClassModel->getCollection()->setClassTypeFilter(\Magento\Tax\Model\ClassModel::TAX_CLASS_TYPE_PRODUCT)->load();
        foreach ($productTaxClassCollection as $productTaxClassItem) {
            $request = $this->getRateRequest($productTaxClassItem->getId());
            $rate = $this->taxCalculation->getRate($request);
            $rate /= 100;
            $this->insertRate($productTaxClassItem->getId(), $storeId, $rate);
        }
    }

    protected function getRateRequest($productTaxClassId)
    {
        if (!isset($this->_rateRequest)) {
            $storeId = $this->getStoreId();
            $store = $this->getStore();
            $countryId = $this->helper->getStoreConfig($storeId, \Magento\Tax\Model\Config::CONFIG_XML_PATH_DEFAULT_COUNTRY);
            $regionId = $this->helper->getStoreConfig($storeId, \Magento\Tax\Model\Config::CONFIG_XML_PATH_DEFAULT_REGION);
            $postcode = $this->helper->getStoreConfig($storeId, \Magento\Tax\Model\Config::CONFIG_XML_PATH_DEFAULT_POSTCODE);
            $request = new\Magento\Framework\DataObject();
            $request->setCountryId($countryId)->setRegionId($regionId)->setPostcode($postcode)->setStore($store)->setCustomerClassId($this->taxCalculation->getDefaultCustomerTaxClass($store));
            $this->_rateRequest = $request;
        }
        $this->_rateRequest->setProductClassId($productTaxClassId);
        return $this->_rateRequest;
    }

    protected function insertRate($productTaxClassId, $storeId, $rate)
    {
        $this->getConnection()->beginTransaction();
        $this->helper->log(__("Insert row to nostress_koongo_cache_tax tax_class_id: %1 store_id: %2 tax_percent: %3", $productTaxClassId, $this->getStoreId(), $rate));
        try {
            $this->getConnection()->insert($this->getMainTable(), ["tax_class_id" => $productTaxClassId, "store_id" => $storeId, "tax_percent" => $rate]);
            $this->getConnection()->commit();
        } catch (\Exception$e) {
            $this->getConnection()->rollBack();
            throw$e;
        }
        return $this;
    }

    protected function cleanMainTable()
    {
        $this->helper->log(__("Clean nostress_koongo_cache_tax records for store #%1", $this->getStoreId()));
        $this->getConnection()->delete($this->getMainTable(), ["store_id = ?" => $this->getStoreId()]);
    }
}

?>