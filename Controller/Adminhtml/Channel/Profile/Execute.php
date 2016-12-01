<?php /*Magento Module developed by NoStress Commerce NOTICE OF LICENSE This source file is subject to the Open Software License (OSL 3.0) that is bundled with this package in the file LICENSE.txt. It is also available through the world-wide-web at this URL: http://opensource.org/licenses/osl-3.0.php If you did of the license and are unable to obtain it through the world-wide-web, please send an email to info@nostresscommerce.cz so we can send you a copy immediately. @copyright Copyright (c) 2015 NoStress Commerce (http://www.nostresscommerce.cz)*/
namespace Nostress\Koongo\Controller\Adminhtml\Channel\Profile;

use Magento\Framework\Controller\ResultFactory;

class Execute extends SaveAbstract
{
    protected $_auth_label = 'Nostress_Koongo::execute';

    public function execute()
    {
        $id = $this->getRequest()->getParam("entity_id");
        if (!empty($id) && is_numeric($id)) {
            $profiles = $this->manager->runProfilesByIds([$id], false);
            $profile = $profiles->getFirstItem();
            $showPreview = $id;
            $status = $profile->getStatus();
            if ($status == \Nostress\Koongo\Model\Channel\Profile::STATUS_ERROR) {
                $this->messageManager->addError($this->getErrorRunMessage($profile->getId()) . $this->translation->replaceActionLinks($profile->getMessage()));
                $showPreview = null;
            } else if ($status == \Nostress\Koongo\Model\Channel\Profile::STATUS_DISABLED) {
                $this->messageManager->addSuccess($this->getDisabledProfileMessage($profile->getId()));
            } else {
                $this->messageManager->addSuccess($this->getSuccessRunMessage($profile->getId()));
            }
        }
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath("*/*/", array("sp" => $showPreview));
    }

    protected function getErrorRunMessage($id)
    {
        return __("Profile #%1 finished with error:", $id) . " ";
    }

    protected function getSuccessRunMessage($id)
    {
        return __("Profile #%1 has been successfully executed.", $id) . " ";
    }

    protected function getDisabledProfileMessage($id)
    {
        return __("Profile #%1 is disabled.", $id) . " ";
    }
} ?>