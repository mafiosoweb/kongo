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
namespace Nostress\Koongo\Controller\Adminhtml\Channel\Profile;

use Magento\Framework\Controller\ResultFactory;

class Flatprocess extends \Magento\Backend\App\Action
{
    protected $resultForwardFactory;
    protected $manager;
    protected $indexerProductFlat;
    protected $_cacheFrontendPool;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory, \Nostress\Koongo\Model\Channel\Profile\Manager $manager, \Magento\Catalog\Model\Indexer\Product\Flat $indexerProductFlat, \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool)
    {
        $this->resultForwardFactory = $resultForwardFactory;
        $this->manager = $manager;
        $this->indexerProductFlat = $indexerProductFlat;
        $this->_cacheFrontendPool = $cacheFrontendPool;
        parent::__construct($context);
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed("Nostress_Koongo::execute");
    }

    public function execute()
    {
        $profileIds = [];
        try {
            $result = $this->manager->addProfilesAttributesToFlat();
            if (!empty($result["profile_ids"])) {
                $profileIds = $result["profile_ids"];
            }
            if (!empty($result["attributes"])) {
                $attributes = $result["attributes"];
                $this->messageManager->addSuccess(__("Following attributes have been added to Product Flat Catalog: %1", implode(", ", $attributes)));
                $this->indexerProductFlat->executeFull();
                $this->messageManager->addSuccess(__("The product flat index has been reindexed."));
                $this->_eventManager->dispatch("adminhtml_cache_flush_all");
                foreach ($this->_cacheFrontendPool as $cacheFrontend) {
                    $cacheFrontend->getBackend()->clean();
                }
                $this->messageManager->addSuccess(__("The cache storage has been flushed."));
            }
        } catch (\Exception$e) {
            $this->messageManager->addError(__("Following error occurred during processing of product attributes: %1", $e->getMessage()));
        }
        if (!empty($profileIds)) {
            if (count($profileIds) == 1) {
                $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                return $resultRedirect->setPath("*/*/execute", ["entity_id" => $profileIds[0], "_current" => true]);
            } else$this->messageManager->addNotice(__("Please execute profiles with following ids: %1", implode(", ", $profileIds)));
        }
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath("*/*/");
    }
}

?>