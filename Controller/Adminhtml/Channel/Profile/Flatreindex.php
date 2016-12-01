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

class Flatreindex extends \Magento\Backend\App\Action
{
    protected $resultForwardFactory;
    protected $manager;
    protected $indexerProductFlat;
    protected $indexerCategoryFlat;
    protected $_cacheFrontendPool;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory, \Nostress\Koongo\Model\Channel\Profile\Manager $manager, \Magento\Catalog\Model\Indexer\Product\Flat $indexerProductFlat, \Magento\Catalog\Model\Indexer\Category\Flat $indexerCategoryFlat, \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool)
    {
        $this->resultForwardFactory = $resultForwardFactory;
        $this->manager = $manager;
        $this->indexerProductFlat = $indexerProductFlat;
        $this->indexerCategoryFlat = $indexerCategoryFlat;
        $this->_cacheFrontendPool = $cacheFrontendPool;
        parent::__construct($context);
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed("Nostress_Koongo::execute");
    }

    public function execute()
    {
        try {
            $this->indexerCategoryFlat->executeFull();
            $this->indexerProductFlat->executeFull();
            $this->messageManager->addSuccess(__("The product flat index has been reindexed."));
            $this->_eventManager->dispatch("adminhtml_cache_flush_all");
            foreach ($this->_cacheFrontendPool as $cacheFrontend) {
                $cacheFrontend->getBackend()->clean();
            }
            $this->messageManager->addSuccess(__("The cache storage has been flushed."));
            $this->manager->runProfilesWithErrorStatus();
            $this->messageManager->addSuccess(__("Profiles with error status were executed."));
        } catch (\Exception$e) {
            $this->messageManager->addError(__("Following error occurred during category and product flat reindex: %1", $e->getMessage()));
        }
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath("*/*/");
    }
}

?>