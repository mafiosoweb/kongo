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

use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Nostress\Koongo\Model\ResourceModel\Channel\Profile\CollectionFactory;
use Magento\Framework\Controller\ResultFactory;

class MassDisable extends \Magento\Backend\App\Action
{
    protected $filter;
    protected $collectionFactory;

    public function __construct(Context $context, Filter $filter, CollectionFactory $collectionFactory)
    {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        foreach ($collection as $item) {
            $item->setStatus(\Nostress\Koongo\Model\Channel\Profile::STATUS_DISABLED);
            $item->save();
        }
        $this->messageManager->addSuccess(__("A total of %1 profile(s) have been disabled.", $collection->getSize()));
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath("*/*/");
    }
}

?>