<?php /*Magento Module developed by NoStress Commerce NOTICE OF LICENSE This source file is subject to the Open Software License (OSL 3.0) that is bundled with this package in the file LICENSE.txt. It is also available through the world-wide-web at this URL: http://opensource.org/licenses/osl-3.0.php If you did of the license and are unable to obtain it through the world-wide-web, please send an email to info@nostresscommerce.cz so we can send you a copy immediately. @copyright Copyright (c) 2015 NoStress Commerce (http://www.nostresscommerce.cz)*/
namespace Nostress\Koongo\Controller\Adminhtml\Channel\Profile;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\UrlInterface;

class Editcategories extends SaveAbstract
{
    protected $_coreRegistry = null;
    protected $session;

    public function __construct(Context $context, PageFactory $resultPageFactory, \Nostress\Koongo\Helper\Version $helper, UrlInterface $urlBuilder, \Nostress\Koongo\Model\Channel\Profile\Manager $manager, \Nostress\Koongo\Model\Channel\ProfileFactory $profileFactory, \Nostress\Koongo\Model\Translation $translation, \Magento\Framework\Registry $registry, \Magento\Backend\Model\Session $session)
    {
        $this->_coreRegistry = $registry;
        $this->session = $session;
        parent::__construct($context, $resultPageFactory, $helper, $urlBuilder, $manager, $profileFactory, $translation);
    }

    protected function _initAction()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu("Nostress_Koongo::koongo");
        $resultPage->addBreadcrumb(__("Koongo"), __("Koongo"));
        $resultPage->addBreadcrumb(__("Manage Category Mapping"), __("Manage Category Mapping"));
        return $resultPage;
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam("entity_id");
        if ($id) {
            $model = $this->profileFactory->create()->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__("This profile no longer exists."));
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath("*/*/");
            }
        }
        $data = $this->session->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }
        $this->_coreRegistry->register("koongo_channel_profile", $model);
        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(__("Edit %1 Category Mapping", $model->getChannel()->getLabel()), __("Edit %1 Category Mapping", $model->getChannel()->getLabel()));
        $title = __("Edit %2 Category Mapping - Profile #%1", $model->getId(), $model->getChannel()->getLabel());
        $resultPage->getConfig()->getTitle()->prepend($title);
        return $resultPage;
    }
} ?>