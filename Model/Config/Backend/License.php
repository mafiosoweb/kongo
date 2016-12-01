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
* @copyright Copyright (c) 2009 NoStress Commerce (http://www.nostresscommerce.cz)
*
*/


namespace Nostress\Koongo\Model\Config\Backend;

class License extends \Magento\Framework\App\Config\Value
{
    /**
     * @var \Nostress\Koongo\Helper\Version
     */
    protected $helper;
    
    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ScopeConfigInterface $config
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param \Nostress\Koongo\Helper\Version $helper
     * @param array $data
     */
    public function __construct(
            \Magento\Framework\Model\Context $context,
            \Magento\Framework\Registry $registry,
            \Magento\Framework\App\Config\ScopeConfigInterface $config,
            \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
            \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
            \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
            \Nostress\Koongo\Helper\Version $helper,
            array $data = []
    ) {
        $this->helper = $helper;
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }
    
    public function afterSave()
    {
        if($this->isValueChanged())
    	{
    		$this->helper->resetLicenseDate();
    	}
    	return parent::afterSave();
    }
}
