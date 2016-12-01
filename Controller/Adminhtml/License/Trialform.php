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
namespace Nostress\Koongo\Controller\Adminhtml\License;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;

class Trialform extends \Magento\Backend\App\Action
{
    protected $_coreRegistry = null;
    protected $session;
    protected $resultPageFactory;

    public function __construct(Action\Context $context, PageFactory $resultPageFactory, \Magento\Framework\Registry $registry, \Magento\Backend\Model\Session $session)
    {
        $this->_coreRegistry = $registry;
        $this->session = $session;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $data = $this->session->getFormData(true);
        if (!empty($data)) {
            $this->_coreRegistry->register("edit_form", $data);
        }
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu("Nostress_Koongo::koongo");
        $resultPage->addBreadcrumb(__("Koongo"), __("Koongo"));
        $resultPage->addBreadcrumb(__("Trial Activation"), __("Trial Activation"));
        $resultPage->getConfig()->getTitle()->prepend(__("Activate Trial"));
        return $resultPage;
    }
}

?>